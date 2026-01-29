<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129213658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT fk_ac6340b358e0a285');
        $this->addSql('DROP INDEX idx_ac6340b358e0a285');
        $this->addSql('ALTER TABLE "like" RENAME COLUMN userid_id TO user_id');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_AC6340B3A76ED395 ON "like" (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B3A76ED395');
        $this->addSql('DROP INDEX IDX_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE "like" RENAME COLUMN user_id TO userid_id');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT fk_ac6340b358e0a285 FOREIGN KEY (userid_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ac6340b358e0a285 ON "like" (userid_id)');
    }
}
