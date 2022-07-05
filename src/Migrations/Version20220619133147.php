<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220619133147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE history ADD action VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE reply_to_id reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE history DROP action');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
