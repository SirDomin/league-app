<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230130232435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE perk_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE perk_stats_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE perk_style_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE perks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE participant (id INT NOT NULL, assists INT NOT NULL, baron_kills INT NOT NULL, bounty_level INT NOT NULL, champ_experience INT NOT NULL, champ_level INT NOT NULL, champion_id INT NOT NULL, champion_name VARCHAR(255) NOT NULL, champion_transform INT NOT NULL, consumables_purchased INT NOT NULL, damage_dealt_to_buildings INT NOT NULL, damage_dealt_to_objectives INT NOT NULL, damage_dealt_to_turrets INT NOT NULL, damage_self_mitigated INT NOT NULL, deaths INT NOT NULL, detector_wards_placed INT NOT NULL, double_kills INT NOT NULL, dragon_kills INT NOT NULL, first_blood_assist BOOLEAN NOT NULL, first_blood_kill BOOLEAN NOT NULL, first_tower_assist BOOLEAN NOT NULL, first_tower_kill BOOLEAN NOT NULL, game_ended_in_early_surrender BOOLEAN NOT NULL, game_ended_in_surrender BOOLEAN NOT NULL, gold_earned INT NOT NULL, gold_spent INT NOT NULL, individual_position VARCHAR(255) NOT NULL, team_position VARCHAR(255) NOT NULL, inhibitor_kills INT NOT NULL, inhibitor_takedowns INT NOT NULL, inhibitors_lost INT NOT NULL, item0 INT NOT NULL, item1 INT NOT NULL, item2 INT NOT NULL, item3 INT NOT NULL, item4 INT NOT NULL, item5 INT NOT NULL, item6 INT NOT NULL, items_purchased INT NOT NULL, killing_sprees INT NOT NULL, kills INT NOT NULL, lane VARCHAR(255) NOT NULL, largest_critical_strike INT NOT NULL, largest_killing_spree INT NOT NULL, largest_multi_kill INT NOT NULL, longest_time_spent_living INT NOT NULL, magic_damage_dealt INT NOT NULL, magic_damage_dealt_to_champions INT NOT NULL, magic_damage_taken INT NOT NULL, neutral_minions_killed INT NOT NULL, nexus_kills INT NOT NULL, nexus_takedowns INT NOT NULL, nexus_lost INT NOT NULL, objectives_stolen INT NOT NULL, objectives_stolen_assists INT NOT NULL, participant_id INT NOT NULL, penta_kills INT NOT NULL, physical_damage_dealt INT NOT NULL, physical_damage_dealt_to_champions INT NOT NULL, physical_damage_taken INT NOT NULL, profile_icon INT NOT NULL, puuid VARCHAR(255) NOT NULL, quadra_kills INT NOT NULL, riot_id_name VARCHAR(255) NOT NULL, riot_id_tagline VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, sight_wards_bought_in_game INT NOT NULL, spell1_casts INT NOT NULL, spell2_casts INT NOT NULL, spell3_casts INT NOT NULL, spell4_casts INT NOT NULL, summoner1_casts INT NOT NULL, summoner1_id INT NOT NULL, summoner2_casts INT NOT NULL, summoner2_id INT NOT NULL, summoner_id VARCHAR(255) NOT NULL, summoner_level INT NOT NULL, summoner_name VARCHAR(255) NOT NULL, team_early_surrendered BOOLEAN NOT NULL, team_id INT NOT NULL, time_ccing_others INT NOT NULL, time_played INT NOT NULL, total_damage_dealt INT NOT NULL, total_damage_dealt_to_champions INT NOT NULL, total_damage_shielded_on_teammates INT NOT NULL, total_damage_taken INT NOT NULL, total_heal INT NOT NULL, total_heals_on_teammates INT NOT NULL, total_minions_killed INT NOT NULL, total_time_ccdealt INT NOT NULL, total_time_spent_dead INT NOT NULL, total_units_healed INT NOT NULL, triple_kills INT NOT NULL, true_damage_dealt INT NOT NULL, true_damage_dealt_to_champions INT NOT NULL, true_damage_taken INT NOT NULL, turret_kills INT NOT NULL, turret_takedowns INT NOT NULL, turrets_lost INT NOT NULL, unreal_kills INT NOT NULL, vision_score INT NOT NULL, vision_wards_bought_in_game INT NOT NULL, wards_killed INT NOT NULL, wards_placed INT NOT NULL, win BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE perk (id INT NOT NULL, stat_perks_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C30ED5EFD14905EA ON perk (stat_perks_id)');
        $this->addSql('CREATE TABLE perk_stats (id INT NOT NULL, defense INT NOT NULL, flex INT NOT NULL, offense INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE perk_style (id INT NOT NULL, perk_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, style INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6CB452A2DF084E33 ON perk_style (perk_id)');
        $this->addSql('CREATE TABLE perks (id INT NOT NULL, perk INT NOT NULL, var1 INT NOT NULL, var2 INT NOT NULL, var3 INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE perk ADD CONSTRAINT FK_C30ED5EFD14905EA FOREIGN KEY (stat_perks_id) REFERENCES perk_stats (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE perk_style ADD CONSTRAINT FK_6CB452A2DF084E33 FOREIGN KEY (perk_id) REFERENCES perk (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE participant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE perk_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE perk_stats_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE perk_style_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE perks_id_seq CASCADE');
        $this->addSql('ALTER TABLE perk DROP CONSTRAINT FK_C30ED5EFD14905EA');
        $this->addSql('ALTER TABLE perk_style DROP CONSTRAINT FK_6CB452A2DF084E33');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE perk');
        $this->addSql('DROP TABLE perk_stats');
        $this->addSql('DROP TABLE perk_style');
        $this->addSql('DROP TABLE perks');
    }
}
