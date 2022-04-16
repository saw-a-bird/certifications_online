<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409122426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, try_id INT NOT NULL, proposition_id INT NOT NULL, is_selected TINYINT(1) NOT NULL, INDEX IDX_50D0C6062D54AEEF (try_id), INDEX IDX_50D0C606DB96F9E (proposition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exams (id INT AUTO_INCREMENT NOT NULL, certification_id INT NOT NULL, code VARCHAR(20) NOT NULL, title VARCHAR(150) NOT NULL, created_at DATE NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_69311328CB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE propositions (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, proposition VARCHAR(255) NOT NULL, is_correct TINYINT(1) NOT NULL, INDEX IDX_E9AB02861E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, exam_id INT NOT NULL, title VARCHAR(50) NOT NULL, task VARCHAR(255) NOT NULL, created_at DATE NOT NULL, INDEX IDX_8ADC54D5578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tries (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exam_id INT NOT NULL, tried_at DATETIME NOT NULL, time_took DATETIME DEFAULT NULL, INDEX IDX_9DC696CEA76ED395 (user_id), INDEX IDX_9DC696CE578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6062D54AEEF FOREIGN KEY (try_id) REFERENCES tries (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606DB96F9E FOREIGN KEY (proposition_id) REFERENCES propositions (id)');
        $this->addSql('ALTER TABLE exams ADD CONSTRAINT FK_69311328CB47068A FOREIGN KEY (certification_id) REFERENCES certifications (id)');
        $this->addSql('ALTER TABLE propositions ADD CONSTRAINT FK_E9AB02861E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id)');
        $this->addSql('ALTER TABLE tries ADD CONSTRAINT FK_9DC696CEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tries ADD CONSTRAINT FK_9DC696CE578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id)');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5578D5E91');
        $this->addSql('ALTER TABLE tries DROP FOREIGN KEY FK_9DC696CE578D5E91');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606DB96F9E');
        $this->addSql('ALTER TABLE propositions DROP FOREIGN KEY FK_E9AB02861E27F6BF');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6062D54AEEF');
        $this->addSql('DROP TABLE answers');
        $this->addSql('DROP TABLE exams');
        $this->addSql('DROP TABLE propositions');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE tries');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
