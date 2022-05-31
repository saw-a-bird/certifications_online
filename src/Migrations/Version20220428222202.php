<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220428222202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE answers');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries ADD score INT NOT NULL, CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, try_id INT NOT NULL, proposition_id INT NOT NULL, is_selected TINYINT(1) NOT NULL, INDEX IDX_50D0C6062D54AEEF (try_id), INDEX IDX_50D0C606DB96F9E (proposition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6062D54AEEF FOREIGN KEY (try_id) REFERENCES tries (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606DB96F9E FOREIGN KEY (proposition_id) REFERENCES propositions (id)');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE comments CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tries DROP score, CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
