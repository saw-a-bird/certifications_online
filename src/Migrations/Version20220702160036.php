<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220702160036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(25) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D2294458B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE complaint_response');
        $this->addSql('ALTER TABLE certification CHANGE e_provider_id e_provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_suggestion CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE exam_title exam_title VARCHAR(100) DEFAULT NULL, CHANGE certification_title certification_title VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP rejected_sugg, CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE complaint_response (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, reponse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type INT NOT NULL, INDEX IDX_22622279B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE complaint_response ADD CONSTRAINT FK_22622279B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('ALTER TABLE certification CHANGE e_provider_id e_provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_suggestion CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE exam_title exam_title VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE certification_title certification_title VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD rejected_sugg INT NOT NULL, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
