<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260605120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add newly exposed Riot match participant and challenge fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE participant ADD caused_game_end_from_ignb_surrender BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD damage_dealt_to_epic_monsters INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD game_ended_in_ignb_surrender BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD missions JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_behavior JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score0 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score1 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score2 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score3 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score4 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score5 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score6 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score7 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score8 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score9 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score10 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD player_score11 DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD position_assigned_by_matchmaking VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD retreat_pings INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD role_bound_item INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD selected_role_preferences VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD team_ignb_surrendered BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD was_premade_with_ignb_game_end_causer BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD was_premade_with_severe_transgressor BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD was_severe_transgressor BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD control_ward_time_coverage_in_river_or_enemy_half DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD earliest_dragon_takedown DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD heal_from_map_sources DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD highest_champion_damage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD highest_crowd_control_score INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD highest_ward_kills INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD jungler_kills_early_jungle INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD kills_on_laners_early_jungle_as_jungler INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD legendary_item_used JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD shortest_time_to_ace_from_first_takedown DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_defeat_aatrox INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_defeat_briar INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_defeat_mini_bosses INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_evolve_weapon INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_have3_passives INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_kill_enemy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_pickup_gold INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_reach_level50 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_survive15_min INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD swarm_win_with5_evolved_weapons INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE challenge DROP swarm_win_with5_evolved_weapons');
        $this->addSql('ALTER TABLE challenge DROP swarm_survive15_min');
        $this->addSql('ALTER TABLE challenge DROP swarm_reach_level50');
        $this->addSql('ALTER TABLE challenge DROP swarm_pickup_gold');
        $this->addSql('ALTER TABLE challenge DROP swarm_kill_enemy');
        $this->addSql('ALTER TABLE challenge DROP swarm_have3_passives');
        $this->addSql('ALTER TABLE challenge DROP swarm_evolve_weapon');
        $this->addSql('ALTER TABLE challenge DROP swarm_defeat_mini_bosses');
        $this->addSql('ALTER TABLE challenge DROP swarm_defeat_briar');
        $this->addSql('ALTER TABLE challenge DROP swarm_defeat_aatrox');
        $this->addSql('ALTER TABLE challenge DROP shortest_time_to_ace_from_first_takedown');
        $this->addSql('ALTER TABLE challenge DROP legendary_item_used');
        $this->addSql('ALTER TABLE challenge DROP kills_on_laners_early_jungle_as_jungler');
        $this->addSql('ALTER TABLE challenge DROP jungler_kills_early_jungle');
        $this->addSql('ALTER TABLE challenge DROP highest_ward_kills');
        $this->addSql('ALTER TABLE challenge DROP highest_crowd_control_score');
        $this->addSql('ALTER TABLE challenge DROP highest_champion_damage');
        $this->addSql('ALTER TABLE challenge DROP heal_from_map_sources');
        $this->addSql('ALTER TABLE challenge DROP earliest_dragon_takedown');
        $this->addSql('ALTER TABLE challenge DROP control_ward_time_coverage_in_river_or_enemy_half');
        $this->addSql('ALTER TABLE participant DROP was_severe_transgressor');
        $this->addSql('ALTER TABLE participant DROP was_premade_with_severe_transgressor');
        $this->addSql('ALTER TABLE participant DROP was_premade_with_ignb_game_end_causer');
        $this->addSql('ALTER TABLE participant DROP team_ignb_surrendered');
        $this->addSql('ALTER TABLE participant DROP selected_role_preferences');
        $this->addSql('ALTER TABLE participant DROP role_bound_item');
        $this->addSql('ALTER TABLE participant DROP retreat_pings');
        $this->addSql('ALTER TABLE participant DROP position_assigned_by_matchmaking');
        $this->addSql('ALTER TABLE participant DROP player_score11');
        $this->addSql('ALTER TABLE participant DROP player_score10');
        $this->addSql('ALTER TABLE participant DROP player_score9');
        $this->addSql('ALTER TABLE participant DROP player_score8');
        $this->addSql('ALTER TABLE participant DROP player_score7');
        $this->addSql('ALTER TABLE participant DROP player_score6');
        $this->addSql('ALTER TABLE participant DROP player_score5');
        $this->addSql('ALTER TABLE participant DROP player_score4');
        $this->addSql('ALTER TABLE participant DROP player_score3');
        $this->addSql('ALTER TABLE participant DROP player_score2');
        $this->addSql('ALTER TABLE participant DROP player_score1');
        $this->addSql('ALTER TABLE participant DROP player_score0');
        $this->addSql('ALTER TABLE participant DROP player_behavior');
        $this->addSql('ALTER TABLE participant DROP missions');
        $this->addSql('ALTER TABLE participant DROP game_ended_in_ignb_surrender');
        $this->addSql('ALTER TABLE participant DROP damage_dealt_to_epic_monsters');
        $this->addSql('ALTER TABLE participant DROP caused_game_end_from_ignb_surrender');
    }
}
