<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204200229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livrable (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT NOT NULL, brief_id INT DEFAULT NULL, immersion_id INT DEFAULT NULL, lien_du_livrable VARCHAR(255) NOT NULL, INDEX IDX_9E78008CC5697D6D (apprenant_id), INDEX IDX_9E78008C757FABFF (brief_id), INDEX IDX_9E78008C22167EA5 (immersion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008CC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008C757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008C22167EA5 FOREIGN KEY (immersion_id) REFERENCES immersion (id)');
        $this->addSql('ALTER TABLE brief_apprenant DROP FOREIGN KEY FK_DD6198ED757FABFF');
        $this->addSql('ALTER TABLE brief_apprenant DROP FOREIGN KEY FK_DD6198EDC5697D6D');
        $this->addSql('DROP TABLE brief_apprenant');
        $this->addSql('ALTER TABLE apprenant DROP FOREIGN KEY FK_C4EB462E22167EA5');
        $this->addSql('DROP INDEX IDX_C4EB462E22167EA5 ON apprenant');
        $this->addSql('ALTER TABLE apprenant DROP immersion_id');
        $this->addSql('ALTER TABLE brief DROP lien_du_livrable');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brief_apprenant (brief_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_DD6198ED757FABFF (brief_id), INDEX IDX_DD6198EDC5697D6D (apprenant_id), PRIMARY KEY(brief_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE brief_apprenant ADD CONSTRAINT FK_DD6198ED757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brief_apprenant ADD CONSTRAINT FK_DD6198EDC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008CC5697D6D');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008C757FABFF');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008C22167EA5');
        $this->addSql('DROP TABLE livrable');
        $this->addSql('ALTER TABLE apprenant ADD immersion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE apprenant ADD CONSTRAINT FK_C4EB462E22167EA5 FOREIGN KEY (immersion_id) REFERENCES immersion (id)');
        $this->addSql('CREATE INDEX IDX_C4EB462E22167EA5 ON apprenant (immersion_id)');
        $this->addSql('ALTER TABLE brief ADD lien_du_livrable VARCHAR(255) NOT NULL');
    }
}
