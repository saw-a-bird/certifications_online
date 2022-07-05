<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220621164237 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75A53A8AA');
        $this->addSql('DROP INDEX IDX_6C3C6D75A53A8AA ON certification');
        $this->addSql('ALTER TABLE certification ADD e_provider_id INT DEFAULT NULL, DROP provider_id');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D753393F3F4 FOREIGN KEY (e_provider_id) REFERENCES e_provider (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D753393F3F4 ON certification (e_provider_id)');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE response_entity_id response_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D753393F3F4');
        $this->addSql('DROP INDEX IDX_6C3C6D753393F3F4 ON certification');
        $this->addSql('ALTER TABLE certification ADD provider_id INT DEFAULT NULL, DROP e_provider_id');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75A53A8AA FOREIGN KEY (provider_id) REFERENCES e_provider (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75A53A8AA ON certification (provider_id)');
        $this->addSql('ALTER TABLE comment CHANGE written_on_id written_on_id INT DEFAULT NULL, CHANGE response_entity_id response_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE e_attempt CHANGE exam_paper_id exam_paper_id INT DEFAULT NULL, CHANGE time_took time_took VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE e_signal CHANGE created_by_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam CHANGE certification_id certification_id INT DEFAULT NULL, CHANGE e_provider_id e_provider_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE exam_paper CHANGE exam_id exam_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proposition CHANGE question_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
