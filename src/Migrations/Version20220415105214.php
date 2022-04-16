<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415105214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification_rates CHANGE is_like_or_dislike is_like_or_dis_like TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comment_rates CHANGE is_like_or_dislike is_like_or_dis_like TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AFFDF7169');
        $this->addSql('ALTER TABLE comments CHANGE reply_to_id reply_to_id INT DEFAULT NULL, CHANGE last_updated_at last_updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification_rates CHANGE is_like_or_dis_like is_like_or_dislike TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE comment_rates CHANGE is_like_or_dis_like is_like_or_dislike TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AFFDF7169');
        $this->addSql('ALTER TABLE comments CHANGE reply_to_id reply_to_id INT DEFAULT NULL, CHANGE last_updated_at last_updated_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
