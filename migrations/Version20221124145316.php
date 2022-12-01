<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124145316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paquet (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(52) NOT NULL, slug BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', restricted TINYINT(1) NOT NULL, created DATETIME NOT NULL, expiration DATETIME NOT NULL, UNIQUE INDEX UNIQ_D0E9B51A77153098 (code), INDEX IDX_D0E9B51A6B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paquet ADD CONSTRAINT FK_D0E9B51A6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paquet DROP FOREIGN KEY FK_D0E9B51A6B3CA4B');
        $this->addSql('DROP TABLE paquet');
        $this->addSql('DROP TABLE user');
    }
}
