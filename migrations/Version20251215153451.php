<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215153451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artiste (id INT AUTO_INCREMENT NOT NULL, nom_scene VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, discr_type VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, user_id INT NOT NULL, discographie_id INT NOT NULL, INDEX IDX_8F91ABF0A76ED395 (user_id), INDEX IDX_8F91ABF05EFE980E (discographie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE discographie (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, date_sortie DATE DEFAULT NULL, pochette VARCHAR(255) DEFAULT NULL, type VARCHAR(20) NOT NULL, artiste_id INT NOT NULL, INDEX IDX_2F6F72921D25844 (artiste_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, date_debut DATETIME DEFAULT NULL, lieu VARCHAR(100) DEFAULT NULL, artiste_id INT NOT NULL, INDEX IDX_B26681E21D25844 (artiste_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE groupe (nom_fanclub VARCHAR(50) DEFAULT NULL, couleur_officielle VARCHAR(50) DEFAULT NULL, date_debut DATE DEFAULT NULL, id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE idol (id INT AUTO_INCREMENT NOT NULL, nom_reel VARCHAR(50) DEFAULT NULL, nom_scene VARCHAR(50) DEFAULT NULL, date_naissance DATE DEFAULT NULL, id_artiste_solo INT DEFAULT NULL, UNIQUE INDEX UNIQ_8ED55E72B40915E0 (id_artiste_solo), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE membre_groupe (id INT AUTO_INCREMENT NOT NULL, position VARCHAR(50) DEFAULT NULL, idol_id INT NOT NULL, groupe_id INT NOT NULL, INDEX IDX_9EB01998E3B52F01 (idol_id), INDEX IDX_9EB019987A45358C (groupe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE morceau (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, duree INT DEFAULT NULL, is_title_track TINYINT DEFAULT 0 NOT NULL, discographie_id INT NOT NULL, INDEX IDX_36BB72085EFE980E (discographie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudonyme VARCHAR(50) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_bias (user_id INT NOT NULL, idol_id INT NOT NULL, INDEX IDX_38062692A76ED395 (user_id), INDEX IDX_38062692E3B52F01 (idol_id), PRIMARY KEY (user_id, idol_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF05EFE980E FOREIGN KEY (discographie_id) REFERENCES discographie (id)');
        $this->addSql('ALTER TABLE discographie ADD CONSTRAINT FK_2F6F72921D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E21D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21BF396750 FOREIGN KEY (id) REFERENCES artiste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE idol ADD CONSTRAINT FK_8ED55E72B40915E0 FOREIGN KEY (id_artiste_solo) REFERENCES artiste (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998E3B52F01 FOREIGN KEY (idol_id) REFERENCES idol (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019987A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE morceau ADD CONSTRAINT FK_36BB72085EFE980E FOREIGN KEY (discographie_id) REFERENCES discographie (id)');
        $this->addSql('ALTER TABLE user_bias ADD CONSTRAINT FK_38062692A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_bias ADD CONSTRAINT FK_38062692E3B52F01 FOREIGN KEY (idol_id) REFERENCES idol (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A76ED395');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF05EFE980E');
        $this->addSql('ALTER TABLE discographie DROP FOREIGN KEY FK_2F6F72921D25844');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E21D25844');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21BF396750');
        $this->addSql('ALTER TABLE idol DROP FOREIGN KEY FK_8ED55E72B40915E0');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998E3B52F01');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019987A45358C');
        $this->addSql('ALTER TABLE morceau DROP FOREIGN KEY FK_36BB72085EFE980E');
        $this->addSql('ALTER TABLE user_bias DROP FOREIGN KEY FK_38062692A76ED395');
        $this->addSql('ALTER TABLE user_bias DROP FOREIGN KEY FK_38062692E3B52F01');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE discographie');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE idol');
        $this->addSql('DROP TABLE membre_groupe');
        $this->addSql('DROP TABLE morceau');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_bias');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
