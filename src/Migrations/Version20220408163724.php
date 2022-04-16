<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408163724 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE certifications_user (certifications_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EB9DD550EAD7901D (certifications_id), INDEX IDX_EB9DD550A76ED395 (user_id), PRIMARY KEY(certifications_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certifications_user ADD CONSTRAINT FK_EB9DD550EAD7901D FOREIGN KEY (certifications_id) REFERENCES certifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certifications_user ADD CONSTRAINT FK_EB9DD550A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE enrolled');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enrolled (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, certification_id INT NOT NULL, state VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_19404A04A76ED395 (user_id), INDEX IDX_19404A04CB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE enrolled ADD CONSTRAINT FK_19404A04A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE enrolled ADD CONSTRAINT FK_19404A04CB47068A FOREIGN KEY (certification_id) REFERENCES certifications (id)');
        $this->addSql('DROP TABLE certifications_user');
        $this->addSql('ALTER TABLE certifications CHANGE provider_id provider_id INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
