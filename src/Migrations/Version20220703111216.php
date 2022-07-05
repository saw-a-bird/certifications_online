<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703111216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification CHANGE e_provider_id e_provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_report CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE state status VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE e_suggestion CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE exam_title exam_title VARCHAR(100) DEFAULT NULL, CHANGE certification_title certification_title VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification CHANGE e_provider_id e_provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_report CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE status state VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE e_suggestion CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE exam_title exam_title VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE certification_title certification_title VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
