<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220606070714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comments CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE signaler DROP FOREIGN KEY FK_EF69B32CB47068A');
        $this->addSql('DROP INDEX IDX_EF69B32CB47068A ON signaler');
        $this->addSql('ALTER TABLE signaler CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE certification_id exam_id INT NOT NULL');
        $this->addSql('ALTER TABLE signaler ADD CONSTRAINT FK_EF69B32578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id)');
        $this->addSql('CREATE INDEX IDX_EF69B32578D5E91 ON signaler (exam_id)');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE comments CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exams CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE propositions CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE signaler DROP FOREIGN KEY FK_EF69B32578D5E91');
        $this->addSql('DROP INDEX IDX_EF69B32578D5E91 ON signaler');
        $this->addSql('ALTER TABLE signaler CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE exam_id certification_id INT NOT NULL');
        $this->addSql('ALTER TABLE signaler ADD CONSTRAINT FK_EF69B32CB47068A FOREIGN KEY (certification_id) REFERENCES certifications (id)');
        $this->addSql('CREATE INDEX IDX_EF69B32CB47068A ON signaler (certification_id)');
        $this->addSql('ALTER TABLE tries CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
