<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208165547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant ADD etat TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE association ADD etat TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE entreprise ADD etat TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE immersion DROP lien_du_livrable');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant DROP etat');
        $this->addSql('ALTER TABLE association DROP etat');
        $this->addSql('ALTER TABLE entreprise DROP etat');
        $this->addSql('ALTER TABLE immersion ADD lien_du_livrable VARCHAR(255) NOT NULL');
    }
}
