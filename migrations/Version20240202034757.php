<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240202034757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant ADD telephone VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4EB462EE7927C74 ON apprenant (email)');
        $this->addSql('ALTER TABLE association ADD telephone VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CCE7927C74 ON association (email)');
        $this->addSql('ALTER TABLE entreprise ADD telephone VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60E7927C74 ON entreprise (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C4EB462EE7927C74 ON apprenant');
        $this->addSql('ALTER TABLE apprenant DROP telephone, DROP description');
        $this->addSql('DROP INDEX UNIQ_FD8521CCE7927C74 ON association');
        $this->addSql('ALTER TABLE association DROP telephone, DROP description');
        $this->addSql('DROP INDEX UNIQ_D19FA60E7927C74 ON entreprise');
        $this->addSql('ALTER TABLE entreprise DROP telephone, DROP description');
    }
}
