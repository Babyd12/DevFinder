<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211123713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO administrateur (nom_complet, email, mot_de_passe, roles) VALUES (?, ?, ?, ?)', ['Administrateur', 'admin@admin.com', '$2y$13$Dq3calBXgOHj0x6bgO2WruxtP8g4QmtquOPZtDjujaPfLw9OhXTyK', '["ROLE_ADMIN"]']);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DELETE FROM administrateur WHERE email = ?', ['admin@admin.com']);
    }
}
