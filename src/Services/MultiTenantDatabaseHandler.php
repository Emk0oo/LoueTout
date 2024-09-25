<?php

namespace App\Services;

use App\Connection\DoctrineMultidatabaseConnection;
use App\Entity\Instance;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class MultiTenantDatabaseHandler
{
    private DoctrineMultidatabaseConnection $connection;
    private array $tablesNames;
    private array $manyTablesNames;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly KernelInterface $kernel,
        private readonly EntityManagerInterface $entityManager,
    ) {
        /* @var DoctrineMultidatabaseConnection $doctrineConnection */
        $doctrineConnection = $this->registry->getConnection('default');
        $this->connection = $doctrineConnection;
        $this->generateTablesArrays();
    }

    private function generateTablesArrays(): void
    {
        $this->tablesNames = [];
        $this->manyTablesNames = [];

        $exclude = ['instance'];


        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($metadatas as $metadata) {
            $tableName = $metadata->getTableName();

            if (in_array($tableName, $exclude)) {
                continue;
            }

            $this->tablesNames[$tableName] = [];

            $mappings = $metadata->getAssociationMappings();
            foreach ($mappings as $mapping) {
                if ($mapping instanceof ManyToManyOwningSideMapping) {
                    $joinTableName = $mapping->joinTable->name;
                    $this->manyTablesNames[$joinTableName] = $mapping->joinTableColumns;
                    $this->manyTablesNames[$joinTableName][] = $this->entityManager->getClassMetadata($mapping->sourceEntity)->getTableName();
                    $this->manyTablesNames[$joinTableName][] = $this->entityManager->getClassMetadata($mapping->targetEntity)->getTableName();
                }
            }
        }
    }

    public function createCredentialsForInstance(Instance $instance): bool
    {
        $this->connection->changeDatabase([
            'dbname' => $instance->getSqlDbName(),
        ]);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        
        $arguments = [
            'command' => 'doctrine:database:create',
            '--if-not-exists' => null,
            '--no-interaction' => null,
            '--connection' => 'default',
        ];
        
        $commandInput = new ArrayInput($arguments);
        
        try {
            $username = $instance->getSqlUserName();
            $password = $instance->getSqlDbPass();

            $application->run($commandInput, new NullOutput());

            $this->connection->executeStatement("
            DROP USER IF EXISTS {$username};
            CREATE USER {$username} WITH PASSWORD '{$password}';
            ");

            unset($application);
            unset($kernel);
        } catch (\Exception $_) {
            return false;
        }


        $this->connection->changeDatabase([
            'dbname' => 'app',
        ]);

        $instance->setDbCreated(true);
        $this->entityManager->flush();


        return $instance->isDbCreated();
    }

    /**
     * @throws TenantInstanceDatabaseNotCreatedException
     */
    public function createForeignTablesForInstance(Instance $instance): bool
    {
        if (!$instance->isDbCreated()) {
            throw new TenantInstanceDatabaseNotCreatedException();
        }

        $username = $instance->getSqlUserName();
        $id = $instance->getId();
        $psqlId = $this->generatePsqlIdForInstance($instance);
        $dbname = $instance->getSqlDbName();

        try {
            $this->connection->changeDatabase([
                'dbname' => 'app',
            ]);

            if ($this->createViewsForInstance($instance)) {
                $this->connection->changeDatabase([
                    'dbname' => $instance->getSqlDbName(),
                ]);


                $this->connection->executeStatement("
                    CREATE EXTENSION IF NOT EXISTS postgres_fdw;
                    CREATE SERVER IF NOT EXISTS app_fdw FOREIGN DATA WRAPPER postgres_fdw OPTIONS (host '127.0.0.1', port '5432', dbname 'app');
                    CREATE USER MAPPING IF NOT EXISTS FOR user SERVER app_fdw OPTIONS (user 'user');
                    CREATE USER MAPPING IF NOT EXISTS FOR {$username} SERVER app_fdw OPTIONS (user '{$username}', password_required 'false');

                    CREATE OR REPLACE FUNCTION override_id() RETURNS trigger as \$override_id\$
                        BEGIN
                            NEW.instance_id := '{$id}';
                            RETURN NEW;
                        END;
                    \$override_id\$
                    LANGUAGE plpgsql;
                ");

                foreach ($this->tablesNames as $name => $options) {
                    $view_name = "{$name}_{$psqlId}";
                    $function_name = "{$name}_id";

                    $this->connection->executeStatement("
                        DROP FOREIGN TABLE IF EXISTS \"{$name}\";
                        IMPORT FOREIGN SCHEMA public
                        LIMIT TO ({$view_name})
                        FROM SERVER app_fdw INTO public;
                        ALTER TABLE {$view_name} RENAME TO \"{$name}\";

                        CREATE OR REPLACE TRIGGER {$function_name}
                        BEFORE INSERT
                        ON \"{$name}\"
                        FOR EACH ROW
                        EXECUTE PROCEDURE override_id();
                    ");
                }

                foreach ($this->manyTablesNames as $name => $options) {
                    $view_name = "{$name}_{$psqlId}";

                    $this->connection->executeStatement("
                        DROP FOREIGN TABLE IF EXISTS \"{$name}\";
                        IMPORT FOREIGN SCHEMA public
                        LIMIT TO ({$view_name})
                        FROM SERVER app_fdw INTO public;
                        ALTER TABLE {$view_name} RENAME TO \"{$name}\";
                    ");
                }

                // Grant privileges to user on database
                $this->connection->executeStatement("
                    GRANT ALL ON SCHEMA public TO {$username};
                    GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO {$username};
                    GRANT ALL PRIVILEGES ON DATABASE {$dbname} TO {$username};
                ");

                return true;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return false;
    }

    private function generatePsqlIdForInstance(Instance $instance): string
    {
        return str_replace('-', '_', md5($instance->getName()));
    }

    /**
     * @throws Exception
     */
    private function createViewsForInstance(Instance $instance): bool
    {
        $this->connection->changeDatabase([
            'dbname' => 'app',
        ]);

        $username = $instance->getSqlUserName();
        $id = $instance->getId();
        $psqlId = $this->generatePsqlIdForInstance($instance);
        
        foreach ($this->tablesNames as $name => $options) {
            $view_name = "{$name}_{$psqlId}";
            
            $this->connection->executeStatement("
                DROP VIEW IF EXISTS {$view_name};
                CREATE VIEW {$view_name} AS SELECT * FROM \"{$name}\" WHERE id = '{$id}' OR instance_id = '{$id}';
                GRANT SELECT, INSERT, UPDATE, DELETE on {$view_name} TO {$username};
            ");
        }

        foreach ($this->manyTablesNames as $name => $options) {
            $view_name = "{$name}_{$psqlId}";

            $this->connection->executeStatement("
                DROP VIEW IF EXISTS {$view_name};
                CREATE VIEW {$view_name} AS SELECT * FROM {$name}
                WHERE {$options[0]} IN (SELECT id FROM {$options[2]} WHERE id = '{$id}' or instance_id = '{$id}')
                    OR {$options[1]} IN (SELECT id FROM {$options[3]} WHERE id = '{$id}');
                GRANT SELECT, INSERT, UPDATE, DELETE on {$view_name} TO {$username};
            ");
        }
        
        return true;
    }
}
