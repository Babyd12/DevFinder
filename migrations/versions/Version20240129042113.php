<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129042113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entreprise_apprenant (entreprise_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_596BE187A4AEAFEA (entreprise_id), INDEX IDX_596BE187C5697D6D (apprenant_id), PRIMARY KEY(entreprise_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687FC5697D6D');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687FC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94D4687F6C6E55B5 ON competence (nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187A4AEAFEA');
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187C5697D6D');
        $this->addSql('DROP TABLE entreprise_apprenant');
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687FC5697D6D');
        $this->addSql('DROP INDEX UNIQ_94D4687F6C6E55B5 ON competence');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687FC5697D6D FOREIGN KEY (apprenant_id) REFERENCES competence (id)');
    }
}
