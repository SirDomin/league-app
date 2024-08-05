<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714103210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant ADD total_ally_jungle_minions_killed INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD total_enemy_jungle_minions_killed INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD placement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment1 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment2 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment3 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment4 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment5 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_augment6 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_subteam_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD subteam_placement INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE participant DROP total_ally_jungle_minions_killed');
        $this->addSql('ALTER TABLE participant DROP total_enemy_jungle_minions_killed');
        $this->addSql('ALTER TABLE participant DROP placement');
        $this->addSql('ALTER TABLE participant DROP player_augment1');
        $this->addSql('ALTER TABLE participant DROP player_augment2');
        $this->addSql('ALTER TABLE participant DROP player_augment3');
        $this->addSql('ALTER TABLE participant DROP player_augment4');
        $this->addSql('ALTER TABLE participant DROP player_augment5');
        $this->addSql('ALTER TABLE participant DROP player_augment6');
        $this->addSql('ALTER TABLE participant DROP player_subteam_id');
        $this->addSql('ALTER TABLE participant DROP subteam_placement');
    }
}
