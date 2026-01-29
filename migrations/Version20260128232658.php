<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260128232658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT fk_ac6340b39d86650f');
        $this->addSql('DROP INDEX idx_ac6340b39d86650f');
        $this->addSql('ALTER TABLE "like" RENAME COLUMN user_id_id TO userid_id');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B358E0A285 FOREIGN KEY (userid_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_AC6340B358E0A285 ON "like" (userid_id)');
        $this->addSql('ALTER TABLE "user" ADD display_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO UNIQ_IDENTIFIER_USERNAME');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B358E0A285');
        $this->addSql('DROP INDEX IDX_AC6340B358E0A285');
        $this->addSql('ALTER TABLE "like" RENAME COLUMN userid_id TO user_id_id');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT fk_ac6340b39d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ac6340b39d86650f ON "like" (user_id_id)');
        $this->addSql('ALTER TABLE "user" DROP display_name');
        $this->addSql('ALTER INDEX uniq_identifier_username RENAME TO uniq_8d93d649f85e0677');
    }
}
