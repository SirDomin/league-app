<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260605121500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change Riot player score and map heal fields to floats';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE participant ALTER player_score0 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score1 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score2 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score3 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score4 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score5 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score6 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score7 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score8 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score9 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score10 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE participant ALTER player_score11 TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER heal_from_map_sources TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE challenge ALTER heal_from_map_sources TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score11 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score10 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score9 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score8 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score7 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score6 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score5 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score4 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score3 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score2 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score1 TYPE INT');
        $this->addSql('ALTER TABLE participant ALTER player_score0 TYPE INT');
    }
}
