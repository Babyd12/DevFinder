<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240202081451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4EB462E450FF010 ON apprenant (telephone)');
        $this->addSql('ALTER TABLE association ADD numero_identification_naitonal VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CC450FF010 ON association (telephone)');
        $this->addSql('ALTER TABLE entreprise ADD numero_identification_naitonal VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60450FF010 ON entreprise (telephone)');
        $this->addSql('ALTER TABLE immersion DROP description, DROP niveau_de_competence');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87F98F226C6E55B5 ON langage_de_programmation (nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C4EB462E450FF010 ON apprenant');
        $this->addSql('DROP INDEX UNIQ_FD8521CC450FF010 ON association');
        $this->addSql('ALTER TABLE association DROP numero_identification_naitonal');
        $this->addSql('DROP INDEX UNIQ_D19FA60450FF010 ON entreprise');
        $this->addSql('ALTER TABLE entreprise DROP numero_identification_naitonal');
        $this->addSql('ALTER TABLE immersion ADD description VARCHAR(255) NOT NULL, ADD niveau_de_competence VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_87F98F226C6E55B5 ON langage_de_programmation');
    }
}
