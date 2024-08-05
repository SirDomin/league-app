<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714104449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ADD earliest_baron DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD fastest_legendary DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD first_turret_killed_time DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD baron_buff_gold_advantage_over_threshold INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD first_turret_killed INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD fist_bump_participation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD killing_sprees INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD mejais_full_stack_in_time DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD two_wards_one_sweeper_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD void_monster_kill INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE challenge DROP earliest_baron');
        $this->addSql('ALTER TABLE challenge DROP fastest_legendary');
        $this->addSql('ALTER TABLE challenge DROP first_turret_killed_time');
        $this->addSql('ALTER TABLE challenge DROP baron_buff_gold_advantage_over_threshold');
        $this->addSql('ALTER TABLE challenge DROP first_turret_killed');
        $this->addSql('ALTER TABLE challenge DROP fist_bump_participation');
        $this->addSql('ALTER TABLE challenge DROP killing_sprees');
        $this->addSql('ALTER TABLE challenge DROP mejais_full_stack_in_time');
        $this->addSql('ALTER TABLE challenge DROP two_wards_one_sweeper_count');
        $this->addSql('ALTER TABLE challenge DROP void_monster_kill');
    }
}
