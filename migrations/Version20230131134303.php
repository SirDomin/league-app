<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131134303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ALTER jungle_cs_before10_minutes TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER max_cs_advantage_on_lane_opponent TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER more_enemy_jungle_than_opponent TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE challenge ALTER jungle_cs_before10_minutes TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER max_cs_advantage_on_lane_opponent TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER more_enemy_jungle_than_opponent TYPE INT');
    }
}
