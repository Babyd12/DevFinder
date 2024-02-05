<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205152320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apprenant (id INT AUTO_INCREMENT NOT NULL, immersion_id INT DEFAULT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_C4EB462E22167EA5 (immersion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apprenant_projet (apprenant_id INT NOT NULL, projet_id INT NOT NULL, INDEX IDX_498E5F05C5697D6D (apprenant_id), INDEX IDX_498E5F05C18272 (projet_id), PRIMARY KEY(apprenant_id, projet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brief (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, lient_support VARCHAR(255) NOT NULL, niveau_de_competence VARCHAR(255) NOT NULL, lien_du_livrable VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brief_apprenant (brief_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_DD6198ED757FABFF (brief_id), INDEX IDX_DD6198EDC5697D6D (apprenant_id), PRIMARY KEY(brief_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_94D4687FC5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise_apprenant (entreprise_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_596BE187A4AEAFEA (entreprise_id), INDEX IDX_596BE187C5697D6D (apprenant_id), PRIMARY KEY(entreprise_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE immersion (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, lien_support VARCHAR(255) NOT NULL, niveau_de_competence VARCHAR(255) NOT NULL, lien_du_livrable VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langage_de_programmation (id INT AUTO_INCREMENT NOT NULL, association_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_87F98F22EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, association_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, nombre_de_participant INT NOT NULL, date_limite DATE NOT NULL, statu VARCHAR(255) NOT NULL, INDEX IDX_50159CA9EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet_langage_de_programmation (projet_id INT NOT NULL, langage_de_programmation_id INT NOT NULL, INDEX IDX_282F68E8C18272 (projet_id), INDEX IDX_282F68E825F29905 (langage_de_programmation_id), PRIMARY KEY(projet_id, langage_de_programmation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apprenant ADD CONSTRAINT FK_C4EB462E22167EA5 FOREIGN KEY (immersion_id) REFERENCES immersion (id)');
        $this->addSql('ALTER TABLE apprenant_projet ADD CONSTRAINT FK_498E5F05C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apprenant_projet ADD CONSTRAINT FK_498E5F05C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brief_apprenant ADD CONSTRAINT FK_DD6198ED757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brief_apprenant ADD CONSTRAINT FK_DD6198EDC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687FC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE langage_de_programmation ADD CONSTRAINT FK_87F98F22EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE projet_langage_de_programmation ADD CONSTRAINT FK_282F68E8C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_langage_de_programmation ADD CONSTRAINT FK_282F68E825F29905 FOREIGN KEY (langage_de_programmation_id) REFERENCES langage_de_programmation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant DROP FOREIGN KEY FK_C4EB462E22167EA5');
        $this->addSql('ALTER TABLE apprenant_projet DROP FOREIGN KEY FK_498E5F05C5697D6D');
        $this->addSql('ALTER TABLE apprenant_projet DROP FOREIGN KEY FK_498E5F05C18272');
        $this->addSql('ALTER TABLE brief_apprenant DROP FOREIGN KEY FK_DD6198ED757FABFF');
        $this->addSql('ALTER TABLE brief_apprenant DROP FOREIGN KEY FK_DD6198EDC5697D6D');
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687FC5697D6D');
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187A4AEAFEA');
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187C5697D6D');
        $this->addSql('ALTER TABLE langage_de_programmation DROP FOREIGN KEY FK_87F98F22EFB9C8A5');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9EFB9C8A5');
        $this->addSql('ALTER TABLE projet_langage_de_programmation DROP FOREIGN KEY FK_282F68E8C18272');
        $this->addSql('ALTER TABLE projet_langage_de_programmation DROP FOREIGN KEY FK_282F68E825F29905');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE apprenant');
        $this->addSql('DROP TABLE apprenant_projet');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE brief');
        $this->addSql('DROP TABLE brief_apprenant');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE entreprise_apprenant');
        $this->addSql('DROP TABLE immersion');
        $this->addSql('DROP TABLE langage_de_programmation');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE projet_langage_de_programmation');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}