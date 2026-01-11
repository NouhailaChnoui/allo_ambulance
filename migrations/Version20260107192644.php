<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260107192644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ambulance (id INT AUTO_INCREMENT NOT NULL, immatriculation VARCHAR(50) NOT NULL, marque VARCHAR(100) NOT NULL, modele VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, disponible TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, ambulance_id INT DEFAULT NULL, driver_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, adresse_depart VARCHAR(255) NOT NULL, adresse_destination VARCHAR(255) NOT NULL, nombre_patients INT DEFAULT NULL, statut VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', notes LONGTEXT DEFAULT NULL, INDEX IDX_2694D7A519EB6921 (client_id), INDEX IDX_2694D7A5EF55E5E1 (ambulance_id), INDEX IDX_2694D7A5C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse VARCHAR(255) NOT NULL, permis VARCHAR(50) NOT NULL, disponible TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A519EB6921 FOREIGN KEY (client_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5EF55E5E1 FOREIGN KEY (ambulance_id) REFERENCES ambulance (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A519EB6921');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5EF55E5E1');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5C3423909');
        $this->addSql('DROP TABLE ambulance');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
