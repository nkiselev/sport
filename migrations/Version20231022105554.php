<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231022105554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE championship_positions (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, team_id INT NOT NULL, position SMALLINT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B1EC4AC394DDBCE9 (championship_id), INDEX IDX_B1EC4AC3296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE championship_scores (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, championship_id INT NOT NULL, score_group_id INT DEFAULT NULL, type SMALLINT NOT NULL COMMENT \'Type of game - group / quarterfinal / semifinal / final\', score INT DEFAULT 0 NOT NULL COMMENT \'Number of points earned by a team in this championship\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_70582337296CD8AE (team_id), INDEX IDX_7058233794DDBCE9 (championship_id), INDEX IDX_70582337957063F (score_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE championships (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, game_group_id INT DEFAULT NULL, team_a_id INT NOT NULL, team_b_id INT NOT NULL, type SMALLINT NOT NULL COMMENT \'Type of game - group / quarterfinal / semifinal / final\', goals_a SMALLINT NOT NULL, goals_b SMALLINT NOT NULL, score_a SMALLINT NOT NULL, score_b SMALLINT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FF232B3194DDBCE9 (championship_id), INDEX IDX_FF232B3155102661 (game_group_id), INDEX IDX_FF232B31EA3FA723 (team_a_id), INDEX IDX_FF232B31F88A08CD (team_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `groups` (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F06D397094DDBCE9 (championship_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, strength SMALLINT NOT NULL COMMENT \'Сила команды, вероятность с которой она победит, чтобы не было совсем скучно\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_group (team_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_AAC60D85296CD8AE (team_id), INDEX IDX_AAC60D85FE54D947 (group_id), PRIMARY KEY(team_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE championship_positions ADD CONSTRAINT FK_B1EC4AC394DDBCE9 FOREIGN KEY (championship_id) REFERENCES championships (id)');
        $this->addSql('ALTER TABLE championship_positions ADD CONSTRAINT FK_B1EC4AC3296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE championship_scores ADD CONSTRAINT FK_70582337296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE championship_scores ADD CONSTRAINT FK_7058233794DDBCE9 FOREIGN KEY (championship_id) REFERENCES championships (id)');
        $this->addSql('ALTER TABLE championship_scores ADD CONSTRAINT FK_70582337957063F FOREIGN KEY (score_group_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3194DDBCE9 FOREIGN KEY (championship_id) REFERENCES championships (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3155102661 FOREIGN KEY (game_group_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31EA3FA723 FOREIGN KEY (team_a_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31F88A08CD FOREIGN KEY (team_b_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE `groups` ADD CONSTRAINT FK_F06D397094DDBCE9 FOREIGN KEY (championship_id) REFERENCES championships (id)');
        $this->addSql('ALTER TABLE team_group ADD CONSTRAINT FK_AAC60D85296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_group ADD CONSTRAINT FK_AAC60D85FE54D947 FOREIGN KEY (group_id) REFERENCES `groups` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championship_positions DROP FOREIGN KEY FK_B1EC4AC394DDBCE9');
        $this->addSql('ALTER TABLE championship_positions DROP FOREIGN KEY FK_B1EC4AC3296CD8AE');
        $this->addSql('ALTER TABLE championship_scores DROP FOREIGN KEY FK_70582337296CD8AE');
        $this->addSql('ALTER TABLE championship_scores DROP FOREIGN KEY FK_7058233794DDBCE9');
        $this->addSql('ALTER TABLE championship_scores DROP FOREIGN KEY FK_70582337957063F');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3194DDBCE9');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3155102661');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31EA3FA723');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31F88A08CD');
        $this->addSql('ALTER TABLE `groups` DROP FOREIGN KEY FK_F06D397094DDBCE9');
        $this->addSql('ALTER TABLE team_group DROP FOREIGN KEY FK_AAC60D85296CD8AE');
        $this->addSql('ALTER TABLE team_group DROP FOREIGN KEY FK_AAC60D85FE54D947');
        $this->addSql('DROP TABLE championship_positions');
        $this->addSql('DROP TABLE championship_scores');
        $this->addSql('DROP TABLE championships');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE `groups`');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE team_group');
    }
}
