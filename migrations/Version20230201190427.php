<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201190427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE challenge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE challenge (id INT NOT NULL, participant_id INT DEFAULT NULL, assist_streak_count INT NOT NULL, ability_uses INT NOT NULL, aces_before15_minutes INT NOT NULL, allied_jungle_monster_kills DOUBLE PRECISION NOT NULL, baron_takedowns INT NOT NULL, blast_cone_opposite_opponent_count INT NOT NULL, bounty_gold INT NOT NULL, buffs_stolen INT NOT NULL, complete_support_quest_in_time INT NOT NULL, control_wards_placed INT NOT NULL, damage_per_minute DOUBLE PRECISION NOT NULL, damage_taken_on_team_percentage DOUBLE PRECISION NOT NULL, danced_with_rift_herald INT NOT NULL, deaths_by_enemy_champs INT NOT NULL, dodge_skill_shots_small_window INT NOT NULL, double_aces INT NOT NULL, dragon_takedowns INT NOT NULL, early_laning_phase_gold_exp_advantage INT NOT NULL, effective_heal_and_shielding DOUBLE PRECISION NOT NULL, elder_dragon_kills_with_opposing_soul INT NOT NULL, elder_dragon_multikills INT NOT NULL, enemy_champion_immobilizations INT NOT NULL, enemy_jungle_monster_kills DOUBLE PRECISION NOT NULL, epic_monster_kills_near_enemy_jungler INT NOT NULL, epic_monster_kills_within30_second_of_spawn INT NOT NULL, epic_monster_steals INT NOT NULL, epic_monster_stolen_without_smite INT NOT NULL, flawless_aces INT NOT NULL, full_team_takedown INT NOT NULL, game_length DOUBLE PRECISION NOT NULL, get_takedowns_in_all_lanes_early_jungle_as_laner INT NOT NULL, gold_per_minute DOUBLE PRECISION NOT NULL, had_afk_teammate INT NOT NULL, had_open_nexus INT NOT NULL, immobilize_and_kill_with_ally INT NOT NULL, initial_buff_count INT NOT NULL, initial_crab_count INT NOT NULL, jungle_cs_before10_minutes DOUBLE PRECISION NOT NULL, jungler_takedowns_near_damaged_epic_monster INT NOT NULL, k_turrets_destroyed_before_plates_fall INT NOT NULL, kda DOUBLE PRECISION NOT NULL, kill_after_hidden_with_ally INT NOT NULL, kill_participation DOUBLE PRECISION NOT NULL, killed_champ_took_full_team_damage_survived INT NOT NULL, kills_near_enemy_turret INT NOT NULL, kills_on_other_lanes_early_jungle_as_laner INT NOT NULL, kills_on_recently_healed_by_aram_pack INT NOT NULL, kills_under_own_turret INT NOT NULL, kills_with_help_from_epic_monster INT NOT NULL, knock_enemy_into_team_and_kill INT NOT NULL, land_skill_shots_early_game INT NOT NULL, lane_minions_first10_minutes INT NOT NULL, laning_phase_gold_exp_advantage INT NOT NULL, legendary_count INT NOT NULL, lost_an_inhibitor INT NOT NULL, max_cs_advantage_on_lane_opponent DOUBLE PRECISION NOT NULL, max_kill_deficit INT NOT NULL, max_level_lead_lane_opponent INT NOT NULL, more_enemy_jungle_than_opponent DOUBLE PRECISION NOT NULL, multi_kill_one_spell INT NOT NULL, multi_turret_rift_herald_count INT NOT NULL, multikills INT NOT NULL, multikills_after_aggressive_flash INT NOT NULL, outer_turret_executes_before10_minutes INT NOT NULL, outnumbered_kills INT NOT NULL, outnumbered_nexus_kill INT NOT NULL, perfect_dragon_souls_taken INT NOT NULL, perfect_game INT NOT NULL, pick_kill_with_ally INT NOT NULL, played_champ_select_position INT NOT NULL, poro_explosions INT NOT NULL, quick_cleanse INT NOT NULL, quick_first_turret INT NOT NULL, quick_solo_kills INT NOT NULL, rift_herald_takedowns INT NOT NULL, save_ally_from_death INT NOT NULL, scuttle_crab_kills INT NOT NULL, skillshots_dodged INT NOT NULL, skillshots_hit INT NOT NULL, snowballs_hit INT NOT NULL, solo_baron_kills INT NOT NULL, solo_kills INT NOT NULL, solo_turrets_lategame INT NOT NULL, stealth_wards_placed INT NOT NULL, survived_single_digit_hp_count INT NOT NULL, survived_three_immobilizes_in_fight INT NOT NULL, takedown_on_first_turret INT NOT NULL, takedowns INT NOT NULL, takedowns_after_gaining_level_advantage INT NOT NULL, takedowns_before_jungle_minion_spawn INT NOT NULL, takedowns_first_xminutes INT NOT NULL, takedowns_in_alcove INT NOT NULL, takedowns_in_enemy_fountain INT NOT NULL, team_baron_kills INT NOT NULL, team_damage_percentage DOUBLE PRECISION NOT NULL, team_elder_dragon_kills INT NOT NULL, team_rift_herald_kills INT NOT NULL, three_wards_one_sweeper_count INT NOT NULL, took_large_damage_survived INT NOT NULL, turret_plates_taken INT NOT NULL, turret_takedowns INT NOT NULL, turrets_taken_with_rift_herald INT NOT NULL, twenty_minions_in3_seconds_count INT NOT NULL, unseen_recalls INT NOT NULL, vision_score_advantage_lane_opponent DOUBLE PRECISION NOT NULL, vision_score_per_minute DOUBLE PRECISION NOT NULL, ward_takedowns INT NOT NULL, ward_takedowns_before20_m INT NOT NULL, wards_guarded INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D70989519D1C3019 ON challenge (participant_id)');
        $this->addSql('CREATE TABLE game (id INT NOT NULL, info_id INT DEFAULT NULL, metadata_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C5D8BC1F8 ON game (info_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318CDC9EE959 ON game (metadata_id)');
        $this->addSql('CREATE TABLE info (id INT NOT NULL, game_id INT DEFAULT NULL, game_creation BIGINT NOT NULL, game_duration BIGINT NOT NULL, game_end_timestamp BIGINT DEFAULT NULL, game_uuid BIGINT NOT NULL, game_mode VARCHAR(255) NOT NULL, game_name VARCHAR(255) NOT NULL, game_start_timestamp BIGINT NOT NULL, game_type VARCHAR(255) NOT NULL, game_version VARCHAR(255) NOT NULL, map_id INT NOT NULL, platform_id VARCHAR(255) NOT NULL, queue_id INT NOT NULL, teams JSON DEFAULT NULL, tournament_code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CB893157E48FD905 ON info (game_id)');
        $this->addSql('CREATE TABLE metadata (id INT NOT NULL, game_id INT DEFAULT NULL, data_version VARCHAR(255) NOT NULL, match_id VARCHAR(255) NOT NULL, participants JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F143414E48FD905 ON metadata (game_id)');
        $this->addSql('CREATE TABLE participant (id INT NOT NULL, info_id INT DEFAULT NULL, challenge_id INT DEFAULT NULL, comment VARCHAR(255) DEFAULT \'\' NOT NULL, all_in_pings INT NOT NULL, assist_me_pings INT NOT NULL, bait_pings INT NOT NULL, basic_pings INT NOT NULL, assists INT NOT NULL, baron_kills INT NOT NULL, bounty_level INT NOT NULL, champ_experience INT NOT NULL, champ_level INT NOT NULL, champion_id INT NOT NULL, champion_name VARCHAR(255) NOT NULL, champion_transform INT NOT NULL, command_pings INT NOT NULL, consumables_purchased INT NOT NULL, damage_dealt_to_buildings INT NOT NULL, damage_dealt_to_objectives INT NOT NULL, damage_dealt_to_turrets INT NOT NULL, damage_self_mitigated INT NOT NULL, danger_pings INT NOT NULL, deaths INT NOT NULL, detector_wards_placed INT NOT NULL, double_kills INT NOT NULL, dragon_kills INT NOT NULL, eligible_for_progression BOOLEAN NOT NULL, enemy_missing_pings INT NOT NULL, enemy_vision_pings INT NOT NULL, first_blood_assist BOOLEAN NOT NULL, first_blood_kill BOOLEAN NOT NULL, first_tower_assist BOOLEAN NOT NULL, first_tower_kill BOOLEAN NOT NULL, game_ended_in_early_surrender BOOLEAN NOT NULL, game_ended_in_surrender BOOLEAN NOT NULL, get_back_pings INT NOT NULL, gold_earned INT NOT NULL, gold_spent INT NOT NULL, hold_pings INT NOT NULL, individual_position VARCHAR(255) NOT NULL, team_position VARCHAR(255) NOT NULL, inhibitor_kills INT NOT NULL, inhibitor_takedowns INT NOT NULL, inhibitors_lost INT NOT NULL, item0 INT NOT NULL, item1 INT NOT NULL, item2 INT NOT NULL, item3 INT NOT NULL, item4 INT NOT NULL, item5 INT NOT NULL, item6 INT NOT NULL, items_purchased INT NOT NULL, killing_sprees INT NOT NULL, kills INT NOT NULL, lane VARCHAR(255) NOT NULL, largest_critical_strike INT NOT NULL, largest_killing_spree INT NOT NULL, largest_multi_kill INT NOT NULL, longest_time_spent_living INT NOT NULL, magic_damage_dealt INT NOT NULL, magic_damage_dealt_to_champions INT NOT NULL, magic_damage_taken INT NOT NULL, need_vision_pings INT NOT NULL, neutral_minions_killed INT NOT NULL, nexus_kills INT NOT NULL, nexus_takedowns INT NOT NULL, nexus_lost INT NOT NULL, objectives_stolen INT NOT NULL, objectives_stolen_assists INT NOT NULL, on_my_way_pings INT NOT NULL, participant_id INT NOT NULL, penta_kills INT NOT NULL, perks JSON DEFAULT NULL, physical_damage_dealt INT NOT NULL, physical_damage_dealt_to_champions INT NOT NULL, physical_damage_taken INT NOT NULL, profile_icon INT NOT NULL, push_pings INT NOT NULL, puuid VARCHAR(255) NOT NULL, quadra_kills INT NOT NULL, riot_id_name VARCHAR(255) NOT NULL, riot_id_tagline VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, sight_wards_bought_in_game INT NOT NULL, spell1_casts INT NOT NULL, spell2_casts INT NOT NULL, spell3_casts INT NOT NULL, spell4_casts INT NOT NULL, summoner1_casts INT NOT NULL, summoner1_id INT NOT NULL, summoner2_casts INT NOT NULL, summoner2_id INT NOT NULL, summoner_id VARCHAR(255) NOT NULL, summoner_level INT NOT NULL, summoner_name VARCHAR(255) NOT NULL, team_early_surrendered BOOLEAN NOT NULL, team_id INT NOT NULL, time_ccing_others INT NOT NULL, time_played INT NOT NULL, total_damage_dealt INT NOT NULL, total_damage_dealt_to_champions INT NOT NULL, total_damage_shielded_on_teammates INT NOT NULL, total_damage_taken INT NOT NULL, total_heal INT NOT NULL, total_heals_on_teammates INT NOT NULL, total_minions_killed INT NOT NULL, total_time_ccdealt INT NOT NULL, total_time_spent_dead INT NOT NULL, total_units_healed INT NOT NULL, triple_kills INT NOT NULL, true_damage_dealt INT NOT NULL, true_damage_dealt_to_champions INT NOT NULL, true_damage_taken INT NOT NULL, turret_kills INT NOT NULL, turret_takedowns INT NOT NULL, turrets_lost INT NOT NULL, unreal_kills INT NOT NULL, vision_cleared_pings INT NOT NULL, vision_score INT NOT NULL, vision_wards_bought_in_game INT NOT NULL, wards_killed INT NOT NULL, wards_placed INT NOT NULL, win BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D79F6B115D8BC1F8 ON participant (info_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1198A21AC6 ON participant (challenge_id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D70989519D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C5D8BC1F8 FOREIGN KEY (info_id) REFERENCES info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CDC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B115D8BC1F8 FOREIGN KEY (info_id) REFERENCES info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1198A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE challenge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE info_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE metadata_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE participant_id_seq CASCADE');
        $this->addSql('ALTER TABLE challenge DROP CONSTRAINT FK_D70989519D1C3019');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318C5D8BC1F8');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318CDC9EE959');
        $this->addSql('ALTER TABLE info DROP CONSTRAINT FK_CB893157E48FD905');
        $this->addSql('ALTER TABLE metadata DROP CONSTRAINT FK_4F143414E48FD905');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B115D8BC1F8');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B1198A21AC6');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE metadata');
        $this->addSql('DROP TABLE participant');
    }
}
