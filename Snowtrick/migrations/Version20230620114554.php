<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230620114554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, tricks_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A3B153154 (tricks_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A3B153154 FOREIGN KEY (tricks_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF813B153154');
        $this->addSql('DROP TABLE medias');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medias (id INT AUTO_INCREMENT NOT NULL, tricks_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_12D2AF813B153154 (tricks_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF813B153154 FOREIGN KEY (tricks_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A3B153154');
        $this->addSql('DROP TABLE images');
    }
}
