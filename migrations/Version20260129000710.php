<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129000710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet ALTER content TYPE VARCHAR(280)');
        $this->addSql('ALTER TABLE tweet ALTER image_filename TYPE VARCHAR(280)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet ALTER content TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE tweet ALTER image_filename TYPE VARCHAR(255)');
    }
}
