<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306095208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks ADD description VARCHAR(255) NOT NULL, ADD groupe VARCHAR(255) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD video VARCHAR(255) DEFAULT NULL, DROP trick_description, DROP trick_groupe_trick, DROP trick_video, DROP trick_image, CHANGE trick_date_create date_create DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks ADD trick_description VARCHAR(255) NOT NULL, ADD trick_groupe_trick VARCHAR(255) NOT NULL, ADD trick_video VARCHAR(255) DEFAULT NULL, ADD trick_image VARCHAR(255) DEFAULT NULL, DROP description, DROP groupe, DROP image, DROP video, CHANGE date_create trick_date_create DATETIME DEFAULT CURRENT_TIMESTAMP');
    }
}
