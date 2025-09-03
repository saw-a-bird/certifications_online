<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714125312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, e_provider_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, thumbnail_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6C3C6D752B36786B (title), INDEX IDX_6C3C6D753393F3F4 (e_provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, written_on_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_edited TINYINT(1) NOT NULL, INDEX IDX_9474526CB03A8386 (created_by_id), INDEX IDX_9474526C9E376378 (written_on_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE e_attempt (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exam_paper_id INT DEFAULT NULL, tried_at DATETIME NOT NULL, time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', score INT NOT NULL, question_count INT NOT NULL, INDEX IDX_F97BEC37A76ED395 (user_id), INDEX IDX_F97BEC37A511C752 (exam_paper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE e_provider (id INT AUTO_INCREMENT NOT NULL, thumbnail_path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8E4985105E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE e_report (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, exam_paper_id INT NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5D593ECFB03A8386 (created_by_id), INDEX IDX_5D593ECFA511C752 (exam_paper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE e_stars (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exam_paper_id INT NOT NULL, stars INT NOT NULL, INDEX IDX_7E05312FA76ED395 (user_id), INDEX IDX_7E05312FA511C752 (exam_paper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE e_suggestion (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, e_provider VARCHAR(255) NOT NULL, q_provider VARCHAR(255) NOT NULL, exam_code VARCHAR(50) NOT NULL, exam_title VARCHAR(100) DEFAULT NULL, certification_title VARCHAR(50) DEFAULT NULL, pdf_file VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, mins_until INT NOT NULL, questions_count INT NOT NULL, status VARCHAR(255) DEFAULT NULL, decided_at DATETIME DEFAULT NULL, rejection_reason VARCHAR(60) DEFAULT NULL, INDEX IDX_1E686FF8B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exam (id INT AUTO_INCREMENT NOT NULL, certification_id INT DEFAULT NULL, e_provider_id INT DEFAULT NULL, code VARCHAR(20) NOT NULL, title VARCHAR(150) NOT NULL, updated_at DATETIME DEFAULT NULL, count_q INT NOT NULL, UNIQUE INDEX UNIQ_38BBA6C677153098 (code), UNIQUE INDEX UNIQ_38BBA6C62B36786B (title), INDEX IDX_38BBA6C6CB47068A (certification_id), INDEX IDX_38BBA6C63393F3F4 (e_provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exam_paper (id INT AUTO_INCREMENT NOT NULL, suggested_by_id INT DEFAULT NULL, exam_id INT DEFAULT NULL, q_provider VARCHAR(50) NOT NULL, imported_from VARCHAR(100) DEFAULT NULL, is_locked TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL, stars INT NOT NULL, mins_until INT NOT NULL, INDEX IDX_1CFF66A666290AB1 (suggested_by_id), INDEX IDX_1CFF66A6578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(25) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(25) NOT NULL, INDEX IDX_D2294458B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, action VARCHAR(100) NOT NULL, INDEX IDX_27BA704BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE p_source (id INT AUTO_INCREMENT NOT NULL, exam_paper_id INT NOT NULL, version DOUBLE PRECISION NOT NULL, INDEX IDX_F2A70977A511C752 (exam_paper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, proposition VARCHAR(255) NOT NULL, is_correct TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_C7CDC3531E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, exam_paper_id INT NOT NULL, title VARCHAR(50) NOT NULL, task TEXT NOT NULL, INDEX IDX_B6F7494EA511C752 (exam_paper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', last_login DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_banned TINYINT(1) NOT NULL, avatar_path VARCHAR(255) NOT NULL, specialty VARCHAR(50) NOT NULL, accepted_sugg INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_certification (user_id INT NOT NULL, certification_id INT NOT NULL, INDEX IDX_82B2C025A76ED395 (user_id), INDEX IDX_82B2C025CB47068A (certification_id), PRIMARY KEY(user_id, certification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_exam (user_id INT NOT NULL, exam_id INT NOT NULL, INDEX IDX_423AEA0FA76ED395 (user_id), INDEX IDX_423AEA0F578D5E91 (exam_id), PRIMARY KEY(user_id, exam_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D753393F3F4 FOREIGN KEY (e_provider_id) REFERENCES e_provider (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9E376378 FOREIGN KEY (written_on_id) REFERENCES exam (id)');
        $this->addSql('ALTER TABLE e_attempt ADD CONSTRAINT FK_F97BEC37A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE e_attempt ADD CONSTRAINT FK_F97BEC37A511C752 FOREIGN KEY (exam_paper_id) REFERENCES exam_paper (id)');
        $this->addSql('ALTER TABLE e_report ADD CONSTRAINT FK_5D593ECFB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE e_report ADD CONSTRAINT FK_5D593ECFA511C752 FOREIGN KEY (exam_paper_id) REFERENCES exam_paper (id)');
        $this->addSql('ALTER TABLE e_stars ADD CONSTRAINT FK_7E05312FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE e_stars ADD CONSTRAINT FK_7E05312FA511C752 FOREIGN KEY (exam_paper_id) REFERENCES exam_paper (id)');
        $this->addSql('ALTER TABLE e_suggestion ADD CONSTRAINT FK_1E686FF8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C6CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C63393F3F4 FOREIGN KEY (e_provider_id) REFERENCES e_provider (id)');
        $this->addSql('ALTER TABLE exam_paper ADD CONSTRAINT FK_1CFF66A666290AB1 FOREIGN KEY (suggested_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE exam_paper ADD CONSTRAINT FK_1CFF66A6578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE p_source ADD CONSTRAINT FK_F2A70977A511C752 FOREIGN KEY (exam_paper_id) REFERENCES exam_paper (id)');
        $this->addSql('ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3531E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EA511C752 FOREIGN KEY (exam_paper_id) REFERENCES exam_paper (id)');
        $this->addSql('ALTER TABLE user_certification ADD CONSTRAINT FK_82B2C025A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_certification ADD CONSTRAINT FK_82B2C025CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_exam ADD CONSTRAINT FK_423AEA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_exam ADD CONSTRAINT FK_423AEA0F578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C6CB47068A');
        $this->addSql('ALTER TABLE user_certification DROP FOREIGN KEY FK_82B2C025CB47068A');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D753393F3F4');
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C63393F3F4');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9E376378');
        $this->addSql('ALTER TABLE exam_paper DROP FOREIGN KEY FK_1CFF66A6578D5E91');
        $this->addSql('ALTER TABLE user_exam DROP FOREIGN KEY FK_423AEA0F578D5E91');
        $this->addSql('ALTER TABLE e_attempt DROP FOREIGN KEY FK_F97BEC37A511C752');
        $this->addSql('ALTER TABLE e_report DROP FOREIGN KEY FK_5D593ECFA511C752');
        $this->addSql('ALTER TABLE e_stars DROP FOREIGN KEY FK_7E05312FA511C752');
        $this->addSql('ALTER TABLE p_source DROP FOREIGN KEY FK_F2A70977A511C752');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EA511C752');
        $this->addSql('ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3531E27F6BF');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB03A8386');
        $this->addSql('ALTER TABLE e_attempt DROP FOREIGN KEY FK_F97BEC37A76ED395');
        $this->addSql('ALTER TABLE e_report DROP FOREIGN KEY FK_5D593ECFB03A8386');
        $this->addSql('ALTER TABLE e_stars DROP FOREIGN KEY FK_7E05312FA76ED395');
        $this->addSql('ALTER TABLE e_suggestion DROP FOREIGN KEY FK_1E686FF8B03A8386');
        $this->addSql('ALTER TABLE exam_paper DROP FOREIGN KEY FK_1CFF66A666290AB1');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458B03A8386');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA76ED395');
        $this->addSql('ALTER TABLE user_certification DROP FOREIGN KEY FK_82B2C025A76ED395');
        $this->addSql('ALTER TABLE user_exam DROP FOREIGN KEY FK_423AEA0FA76ED395');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE e_attempt');
        $this->addSql('DROP TABLE e_provider');
        $this->addSql('DROP TABLE e_report');
        $this->addSql('DROP TABLE e_stars');
        $this->addSql('DROP TABLE e_suggestion');
        $this->addSql('DROP TABLE exam');
        $this->addSql('DROP TABLE exam_paper');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE p_source');
        $this->addSql('DROP TABLE proposition');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_certification');
        $this->addSql('DROP TABLE user_exam');
    }
}
