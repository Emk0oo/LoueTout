<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925145344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE instance_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE instance (id UUID NOT NULL, name VARCHAR(255) NOT NULL, sql_db_name VARCHAR(255) DEFAULT NULL, sql_user_name VARCHAR(255) DEFAULT NULL, sql_db_pass VARCHAR(255) DEFAULT NULL, db_created BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN instance.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE instance_settings (id INT NOT NULL, instance_id UUID NOT NULL, key VARCHAR(255) NOT NULL, value VARCHAR(510) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E7EFA7023A51721D ON instance_settings (instance_id)');
        $this->addSql('COMMENT ON COLUMN instance_settings.instance_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE product (id UUID NOT NULL, instance_id UUID NOT NULL, rent_by_id UUID NOT NULL, label VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD3A51721D ON product (instance_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD4B99D2B2 ON product (rent_by_id)');
        $this->addSql('COMMENT ON COLUMN product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product.instance_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product.rent_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE product_image (id UUID NOT NULL, product_id UUID DEFAULT NULL, instance_id UUID NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64617F034584665A ON product_image (product_id)');
        $this->addSql('CREATE INDEX IDX_64617F033A51721D ON product_image (instance_id)');
        $this->addSql('COMMENT ON COLUMN product_image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product_image.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product_image.instance_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE rent_history (id UUID NOT NULL, instance_id UUID NOT NULL, rent_by_id UUID NOT NULL, product_id UUID NOT NULL, price DOUBLE PRECISION NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C6960B3B3A51721D ON rent_history (instance_id)');
        $this->addSql('CREATE INDEX IDX_C6960B3B4B99D2B2 ON rent_history (rent_by_id)');
        $this->addSql('CREATE INDEX IDX_C6960B3B4584665A ON rent_history (product_id)');
        $this->addSql('COMMENT ON COLUMN rent_history.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rent_history.instance_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rent_history.rent_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rent_history.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rent_history.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rent_history.ended_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, instance_id UUID DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, stripe_id VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D93D6493A51721D ON "user" (instance_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".instance_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE instance_settings ADD CONSTRAINT FK_E7EFA7023A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4B99D2B2 FOREIGN KEY (rent_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F033A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rent_history ADD CONSTRAINT FK_C6960B3B3A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rent_history ADD CONSTRAINT FK_C6960B3B4B99D2B2 FOREIGN KEY (rent_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rent_history ADD CONSTRAINT FK_C6960B3B4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6493A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE instance_settings_id_seq CASCADE');
        $this->addSql('ALTER TABLE instance_settings DROP CONSTRAINT FK_E7EFA7023A51721D');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD3A51721D');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD4B99D2B2');
        $this->addSql('ALTER TABLE product_image DROP CONSTRAINT FK_64617F034584665A');
        $this->addSql('ALTER TABLE product_image DROP CONSTRAINT FK_64617F033A51721D');
        $this->addSql('ALTER TABLE rent_history DROP CONSTRAINT FK_C6960B3B3A51721D');
        $this->addSql('ALTER TABLE rent_history DROP CONSTRAINT FK_C6960B3B4B99D2B2');
        $this->addSql('ALTER TABLE rent_history DROP CONSTRAINT FK_C6960B3B4584665A');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6493A51721D');
        $this->addSql('DROP TABLE instance');
        $this->addSql('DROP TABLE instance_settings');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE rent_history');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
