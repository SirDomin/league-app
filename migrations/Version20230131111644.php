<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131111644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE challenge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE challenge (id INT NOT NULL, participant_id INT DEFAULT NULL, assist_streak_count INT NOT NULL, ability_uses INT NOT NULL, aces_before15_minutes INT NOT NULL, allied_jungle_monster_kills INT NOT NULL, baron_takedowns INT NOT NULL, blast_cone_opposite_opponent_count INT NOT NULL, bounty_gold INT NOT NULL, buffs_stolen INT NOT NULL, complete_support_quest_in_time INT NOT NULL, control_wards_placed INT NOT NULL, damage_per_minute DOUBLE PRECISION NOT NULL, damage_taken_on_team_percentage DOUBLE PRECISION NOT NULL, danced_with_rift_herald INT NOT NULL, deaths_by_enemy_champs INT NOT NULL, dodge_skill_shots_small_window INT NOT NULL, double_aces INT NOT NULL, dragon_takedowns INT NOT NULL, early_laning_phase_gold_exp_advantage INT NOT NULL, effective_heal_and_shielding DOUBLE PRECISION NOT NULL, elder_dragon_kills_with_opposing_soul INT NOT NULL, elder_dragon_multikills INT NOT NULL, enemy_champion_immobilizations INT NOT NULL, enemy_jungle_monster_kills INT NOT NULL, epic_monster_kills_near_enemy_jungler INT NOT NULL, epic_monster_kills_within30_second_of_spawn INT NOT NULL, epic_monster_steals INT NOT NULL, epic_monster_stolen_without_smite INT NOT NULL, flawless_aces INT NOT NULL, full_team_takedown INT NOT NULL, game_length DOUBLE PRECISION NOT NULL, get_takedowns_in_all_lanes_early_jungle_as_laner INT NOT NULL, gold_per_minute DOUBLE PRECISION NOT NULL, had_afk_teammate INT NOT NULL, had_open_nexus INT NOT NULL, immobilize_and_kill_with_ally INT NOT NULL, initial_buff_count INT NOT NULL, initial_crab_count INT NOT NULL, jungle_cs_before10_minutes INT NOT NULL, jungler_takedowns_near_damaged_epic_monster INT NOT NULL, k_turrets_destroyed_before_plates_fall INT NOT NULL, kda DOUBLE PRECISION NOT NULL, kill_after_hidden_with_ally INT NOT NULL, kill_participation DOUBLE PRECISION NOT NULL, killed_champ_took_full_team_damage_survived INT NOT NULL, kills_near_enemy_turret INT NOT NULL, kills_on_other_lanes_early_jungle_as_laner INT NOT NULL, kills_on_recently_healed_by_aram_pack INT NOT NULL, kills_under_own_turret INT NOT NULL, kills_with_help_from_epic_monster INT NOT NULL, knock_enemy_into_team_and_kill INT NOT NULL, land_skill_shots_early_game INT NOT NULL, lane_minions_first10_minutes INT NOT NULL, laning_phase_gold_exp_advantage INT NOT NULL, legendary_count INT NOT NULL, lost_an_inhibitor INT NOT NULL, max_cs_advantage_on_lane_opponent INT NOT NULL, max_kill_deficit INT NOT NULL, max_level_lead_lane_opponent INT NOT NULL, more_enemy_jungle_than_opponent INT NOT NULL, multi_kill_one_spell INT NOT NULL, multi_turret_rift_herald_count INT NOT NULL, multikills INT NOT NULL, multikills_after_aggressive_flash INT NOT NULL, outer_turret_executes_before10_minutes INT NOT NULL, outnumbered_kills INT NOT NULL, outnumbered_nexus_kill INT NOT NULL, perfect_dragon_souls_taken INT NOT NULL, perfect_game INT NOT NULL, pick_kill_with_ally INT NOT NULL, played_champ_select_position INT NOT NULL, poro_explosions INT NOT NULL, quick_cleanse INT NOT NULL, quick_first_turret INT NOT NULL, quick_solo_kills INT NOT NULL, rift_herald_takedowns INT NOT NULL, save_ally_from_death INT NOT NULL, scuttle_crab_kills INT NOT NULL, skillshots_dodged INT NOT NULL, skillshots_hit INT NOT NULL, snowballs_hit INT NOT NULL, solo_baron_kills INT NOT NULL, solo_kills INT NOT NULL, solo_turrets_lategame INT NOT NULL, stealth_wards_placed INT NOT NULL, survived_single_digit_hp_count INT NOT NULL, survived_three_immobilizes_in_fight INT NOT NULL, takedown_on_first_turret INT NOT NULL, takedowns INT NOT NULL, takedowns_after_gaining_level_advantage INT NOT NULL, takedowns_before_jungle_minion_spawn INT NOT NULL, takedowns_first_xminutes INT NOT NULL, takedowns_in_alcove INT NOT NULL, takedowns_in_enemy_fountain INT NOT NULL, team_baron_kills INT NOT NULL, team_damage_percentage DOUBLE PRECISION NOT NULL, team_elder_dragon_kills INT NOT NULL, team_rift_herald_kills INT NOT NULL, three_wards_one_sweeper_count INT NOT NULL, took_large_damage_survived INT NOT NULL, turret_plates_taken INT NOT NULL, turret_takedowns INT NOT NULL, turrets_taken_with_rift_herald INT NOT NULL, twenty_minions_in3_seconds_count INT NOT NULL, unseen_recalls INT NOT NULL, vision_score_advantage_lane_opponent DOUBLE PRECISION NOT NULL, vision_score_per_minute DOUBLE PRECISION NOT NULL, ward_takedowns INT NOT NULL, ward_takedowns_before20_m INT NOT NULL, wards_guarded INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D70989519D1C3019 ON challenge (participant_id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D70989519D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant ADD challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD all_in_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD assist_me_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD bait_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD basic_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD command_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD danger_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD eligible_for_progression BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE participant ADD enemy_missing_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD enemy_vision_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD get_back_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD hold_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD need_vision_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD on_my_way_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD push_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD vision_cleared_pings INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1198A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1198A21AC6 ON participant (challenge_id)');
        $this->addSql('ALTER TABLE perks ADD perk_style_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE perks ADD CONSTRAINT FK_2B783E37F83C0411 FOREIGN KEY (perk_style_id) REFERENCES perk_style (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B783E37F83C0411 ON perks (perk_style_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B1198A21AC6');
        $this->addSql('DROP SEQUENCE challenge_id_seq CASCADE');
        $this->addSql('ALTER TABLE challenge DROP CONSTRAINT FK_D70989519D1C3019');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP INDEX UNIQ_D79F6B1198A21AC6');
        $this->addSql('ALTER TABLE participant DROP challenge_id');
        $this->addSql('ALTER TABLE participant DROP all_in_pings');
        $this->addSql('ALTER TABLE participant DROP assist_me_pings');
        $this->addSql('ALTER TABLE participant DROP bait_pings');
        $this->addSql('ALTER TABLE participant DROP basic_pings');
        $this->addSql('ALTER TABLE participant DROP command_pings');
        $this->addSql('ALTER TABLE participant DROP danger_pings');
        $this->addSql('ALTER TABLE participant DROP eligible_for_progression');
        $this->addSql('ALTER TABLE participant DROP enemy_missing_pings');
        $this->addSql('ALTER TABLE participant DROP enemy_vision_pings');
        $this->addSql('ALTER TABLE participant DROP get_back_pings');
        $this->addSql('ALTER TABLE participant DROP hold_pings');
        $this->addSql('ALTER TABLE participant DROP need_vision_pings');
        $this->addSql('ALTER TABLE participant DROP on_my_way_pings');
        $this->addSql('ALTER TABLE participant DROP push_pings');
        $this->addSql('ALTER TABLE participant DROP vision_cleared_pings');
        $this->addSql('ALTER TABLE perks DROP CONSTRAINT FK_2B783E37F83C0411');
        $this->addSql('DROP INDEX UNIQ_2B783E37F83C0411');
        $this->addSql('ALTER TABLE perks DROP perk_style_id');
    }
}
