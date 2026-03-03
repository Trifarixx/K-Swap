<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302142046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste DROP description');
        $this->addSql('ALTER TABLE discographie CHANGE titre titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE groupe DROP couleur_officielle');
        $this->addSql('ALTER TABLE morceau CHANGE titre titre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE discographie CHANGE titre titre VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE groupe ADD couleur_officielle VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE morceau CHANGE titre titre VARCHAR(100) NOT NULL');
    }
}
