<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260111042559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE comment ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "like" ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE private_message ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE tweet ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a803ad8644e');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a80233d34c1');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE comment ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE "like" ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE private_message ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE tweet ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT fk_f7129a803ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT fk_f7129a80233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
