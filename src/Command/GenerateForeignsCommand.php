<?php

namespace App\Command;

use App\Exceptions\TenantUserDatabaseNotCreatedException;
use App\Repository\InstanceRepository;
use App\Services\MultiTenantDatabaseHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:database:foreigns',
    description: 'Generate foreigns views and databases',
    aliases: ['app:generate-foreigns'],
    hidden: false
)]
class GenerateForeignsCommand extends Command
{
    public function __construct(
        private readonly InstanceRepository $instanceRepository,
        private readonly MultiTenantDatabaseHandler $tenancyHandler,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $instances = $this->instanceRepository->findAll();
        $errors = 0;

        foreach ($instances as $instance) {
            try {
                $this->tenancyHandler->createForeignTablesForInstance($instance);
            } catch (TenantUserDatabaseNotCreatedException $_) {
                ++$errors;
            }
        }

        return $errors;
    }
}
