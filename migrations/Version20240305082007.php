<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305082007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles JSON NOT NULL, etat TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apprenant (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles JSON NOT NULL, image VARBINARY(255) DEFAULT NULL, telephone VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, etat TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C4EB462EE7927C74 (email), UNIQUE INDEX UNIQ_C4EB462E450FF010 (telephone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apprenant_projet (apprenant_id INT NOT NULL, projet_id INT NOT NULL, INDEX IDX_498E5F05C5697D6D (apprenant_id), INDEX IDX_498E5F05C18272 (projet_id), PRIMARY KEY(apprenant_id, projet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, numero_identification_naitonal VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_FD8521CCE7927C74 (email), UNIQUE INDEX UNIQ_FD8521CC450FF010 (telephone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brief (id INT AUTO_INCREMENT NOT NULL, nom_fichier VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', titre VARCHAR(255) NOT NULL, lient_support VARCHAR(255) NOT NULL, niveau_de_competence VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description_competence (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT NOT NULL, competence_id INT NOT NULL, description VARCHAR(255) NOT NULL, lien_de_realisation VARCHAR(255) NOT NULL, INDEX IDX_2B38BFBEC5697D6D (apprenant_id), INDEX IDX_2B38BFBE15761DAB (competence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, numero_identification_naitonal VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_D19FA60E7927C74 (email), UNIQUE INDEX UNIQ_D19FA60450FF010 (telephone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise_apprenant (entreprise_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_596BE187A4AEAFEA (entreprise_id), INDEX IDX_596BE187C5697D6D (apprenant_id), PRIMARY KEY(entreprise_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE immersion (id INT AUTO_INCREMENT NOT NULL, nom_fichier VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', titre VARCHAR(255) NOT NULL, lien_support VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langage_de_programmation (id INT AUTO_INCREMENT NOT NULL, association_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_87F98F226C6E55B5 (nom), INDEX IDX_87F98F22EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livrable (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT NOT NULL, brief_id INT DEFAULT NULL, immersion_id INT DEFAULT NULL, lien_du_livrable VARCHAR(255) NOT NULL, INDEX IDX_9E78008CC5697D6D (apprenant_id), INDEX IDX_9E78008C757FABFF (brief_id), INDEX IDX_9E78008C22167EA5 (immersion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, association_id INT DEFAULT NULL, apprenant_id INT DEFAULT NULL, projet_id INT NOT NULL, message VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FEFB9C8A5 (association_id), INDEX IDX_B6BD307FC5697D6D (apprenant_id), INDEX IDX_B6BD307FC18272 (projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, association_id INT NOT NULL, nom_fichier VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', titre VARCHAR(255) NOT NULL, nombre_de_participant VARCHAR(10) DEFAULT NULL, date_limite DATE NOT NULL, lien_du_repertoire_distant VARCHAR(255) DEFAULT NULL, statu VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_50159CA9EFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet_langage_de_programmation (projet_id INT NOT NULL, langage_de_programmation_id INT NOT NULL, INDEX IDX_282F68E8C18272 (projet_id), INDEX IDX_282F68E825F29905 (langage_de_programmation_id), PRIMARY KEY(projet_id, langage_de_programmation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apprenant_projet ADD CONSTRAINT FK_498E5F05C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apprenant_projet ADD CONSTRAINT FK_498E5F05C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE description_competence ADD CONSTRAINT FK_2B38BFBEC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE description_competence ADD CONSTRAINT FK_2B38BFBE15761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entreprise_apprenant ADD CONSTRAINT FK_596BE187C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE langage_de_programmation ADD CONSTRAINT FK_87F98F22EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008CC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008C757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        $this->addSql('ALTER TABLE livrable ADD CONSTRAINT FK_9E78008C22167EA5 FOREIGN KEY (immersion_id) REFERENCES immersion (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FC5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenant (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FC18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE projet_langage_de_programmation ADD CONSTRAINT FK_282F68E8C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_langage_de_programmation ADD CONSTRAINT FK_282F68E825F29905 FOREIGN KEY (langage_de_programmation_id) REFERENCES langage_de_programmation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenant_projet DROP FOREIGN KEY FK_498E5F05C5697D6D');
        $this->addSql('ALTER TABLE apprenant_projet DROP FOREIGN KEY FK_498E5F05C18272');
        $this->addSql('ALTER TABLE description_competence DROP FOREIGN KEY FK_2B38BFBEC5697D6D');
        $this->addSql('ALTER TABLE description_competence DROP FOREIGN KEY FK_2B38BFBE15761DAB');
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187A4AEAFEA');
        $this->addSql('ALTER TABLE entreprise_apprenant DROP FOREIGN KEY FK_596BE187C5697D6D');
        $this->addSql('ALTER TABLE langage_de_programmation DROP FOREIGN KEY FK_87F98F22EFB9C8A5');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008CC5697D6D');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008C757FABFF');
        $this->addSql('ALTER TABLE livrable DROP FOREIGN KEY FK_9E78008C22167EA5');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FEFB9C8A5');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FC5697D6D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FC18272');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9EFB9C8A5');
        $this->addSql('ALTER TABLE projet_langage_de_programmation DROP FOREIGN KEY FK_282F68E8C18272');
        $this->addSql('ALTER TABLE projet_langage_de_programmation DROP FOREIGN KEY FK_282F68E825F29905');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE apprenant');
        $this->addSql('DROP TABLE apprenant_projet');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE brief');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE description_competence');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE entreprise_apprenant');
        $this->addSql('DROP TABLE immersion');
        $this->addSql('DROP TABLE langage_de_programmation');
        $this->addSql('DROP TABLE livrable');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE projet_langage_de_programmation');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
