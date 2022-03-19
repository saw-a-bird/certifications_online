<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220318222504 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_category_id INT DEFAULT NULL, name VARCHAR(25) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_64C19C1796A8F92 (parent_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_category (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E6C1FDC1A76ED395 (user_id), INDEX IDX_E6C1FDC112469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1796A8F92 FOREIGN KEY (parent_category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comm_rates');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE recommendations');
        $this->addSql('DROP TABLE selected_tags');
        $this->addSql('DROP TABLE stars');
        $this->addSql('ALTER TABLE user ADD is_banned TINYINT(1) NOT NULL, ADD avatar_path VARCHAR(100) NOT NULL, ADD biography VARCHAR(125) NOT NULL, ADD specialty VARCHAR(50) NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1796A8F92');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC112469DE2');
        $this->addSql('CREATE TABLE accounts (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME DEFAULT \'NULL\', last_login DATETIME DEFAULT \'NULL\', isBanned TINYINT(1) DEFAULT \'0\' NOT NULL, avatar_path TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, bio VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, specialty VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX username (username), UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE articles (art_id INT AUTO_INCREMENT NOT NULL COMMENT \'article\'\'s ID\', auth_id INT NOT NULL COMMENT \'author\'\'s ID\', title VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME DEFAULT \'current_timestamp()\' NOT NULL COMMENT \'creation date\', date_edit DATETIME DEFAULT \'NULL\' COMMENT \'last edit date\', content TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, stars INT NOT NULL COMMENT \'the number of stars (limited)\', categ_id VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci` COMMENT \'category\'\'s ID\', PRIMARY KEY(art_id, auth_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categories (categ_id INT AUTO_INCREMENT NOT NULL, name_categ VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, parent_id INT DEFAULT NULL, desc_categ VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(categ_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comm_rates (comm_id INT NOT NULL, acc_id INT NOT NULL, rate TINYINT(1) NOT NULL, PRIMARY KEY(acc_id, comm_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comments (comm_id INT AUTO_INCREMENT NOT NULL COMMENT \'the reply/comment\'\'s ID\', art_id INT NOT NULL COMMENT \'the article\'\'s ID\', acc_id INT NOT NULL COMMENT \'The commenter\'\'s ID\', content TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, likes INT DEFAULT 0, dislikes INT DEFAULT 0, last_date DATETIME DEFAULT \'current_timestamp()\' NOT NULL COMMENT \'The last time this reply/commend was modified\', reply_to INT DEFAULT NULL COMMENT \'A reply to #\', isEdited TINYINT(1) DEFAULT \'0\' NOT NULL COMMENT \'Is the comment/reply edited?\', PRIMARY KEY(comm_id, art_id, acc_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE notifications (notf_id INT AUTO_INCREMENT NOT NULL COMMENT \'Notification\'\'s ID\', acc_id INT NOT NULL COMMENT \'Account\'\'s ID\', type INT NOT NULL COMMENT \'Type of notification\', comm_id INT DEFAULT NULL COMMENT \'A reply to a comment?\', art_id INT DEFAULT NULL COMMENT \'A new article from a followed tag(s)?\', PRIMARY KEY(notf_id, acc_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recommendations (art_id INT NOT NULL, rec_id INT NOT NULL, comment VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, PRIMARY KEY(art_id, rec_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE selected_tags (acc_id INT NOT NULL, categ_id INT NOT NULL, priority INT NOT NULL, PRIMARY KEY(acc_id, categ_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stars (art_id INT NOT NULL, acc_id INT NOT NULL, stars TINYINT(1) NOT NULL, PRIMARY KEY(art_id, acc_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('ALTER TABLE user DROP is_banned, DROP avatar_path, DROP biography, DROP specialty, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
    }
}
