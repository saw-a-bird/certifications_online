<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415094457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE certification_rates (id INT AUTO_INCREMENT NOT NULL, certification_id INT NOT NULL, user_id INT NOT NULL, is_like_or_dislike TINYINT(1) NOT NULL, INDEX IDX_F911D73CB47068A (certification_id), INDEX IDX_F911D73A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_rates (id INT AUTO_INCREMENT NOT NULL, comment_id INT NOT NULL, user_id INT NOT NULL, is_like_or_dislike TINYINT(1) NOT NULL, INDEX IDX_ED4E33CDF8697D13 (comment_id), INDEX IDX_ED4E33CDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, written_on_id INT NOT NULL, reply_to_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, last_updated_at DATETIME NOT NULL, is_edited TINYINT(1) NOT NULL, INDEX IDX_5F9E962AB03A8386 (created_by_id), INDEX IDX_5F9E962A9E376378 (written_on_id), INDEX IDX_5F9E962AFFDF7169 (reply_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification_rates ADD CONSTRAINT FK_F911D73CB47068A FOREIGN KEY (certification_id) REFERENCES certifications (id)');
        $this->addSql('ALTER TABLE certification_rates ADD CONSTRAINT FK_F911D73A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_rates ADD CONSTRAINT FK_ED4E33CDF8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE comment_rates ADD CONSTRAINT FK_ED4E33CDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A9E376378 FOREIGN KEY (written_on_id) REFERENCES certifications (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AFFDF7169 FOREIGN KEY (reply_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment_rates DROP FOREIGN KEY FK_ED4E33CDF8697D13');
        $this->addSql('DROP TABLE certification_rates');
        $this->addSql('DROP TABLE comment_rates');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE roles');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
