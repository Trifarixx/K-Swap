<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311104550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste CHANGE nom_scene nom_scene VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE avis ADD morceau_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF029E8E5CE FOREIGN KEY (morceau_id) REFERENCES morceau (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF029E8E5CE ON avis (morceau_id)');
        $this->addSql('ALTER TABLE discographie CHANGE titre titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE morceau CHANGE titre titre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste CHANGE nom_scene nom_scene VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF029E8E5CE');
        $this->addSql('DROP INDEX IDX_8F91ABF029E8E5CE ON avis');
        $this->addSql('ALTER TABLE avis DROP morceau_id');
        $this->addSql('ALTER TABLE discographie CHANGE titre titre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE morceau CHANGE titre titre VARCHAR(255) DEFAULT NULL');
    }
}
