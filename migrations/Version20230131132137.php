<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131132137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE game_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE game (id INT NOT NULL, info_id INT DEFAULT NULL, metadata_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C5D8BC1F8 ON game (info_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318CDC9EE959 ON game (metadata_id)');
        $this->addSql('CREATE TABLE info (id INT NOT NULL, game_id BIGINT NOT NULL, game_creation BIGINT NOT NULL, game_duration BIGINT NOT NULL, game_end_timestamp BIGINT DEFAULT NULL, game_mode VARCHAR(255) NOT NULL, game_name VARCHAR(255) NOT NULL, game_start_timestamp BIGINT NOT NULL, game_type VARCHAR(255) NOT NULL, game_version VARCHAR(255) NOT NULL, map_id INT NOT NULL, platform_id VARCHAR(255) NOT NULL, queue_id INT NOT NULL, teams JSON DEFAULT NULL, tournament_code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CB893157E48FD905 ON info (game_id)');
        $this->addSql('CREATE TABLE metadata (id INT NOT NULL, game_id INT DEFAULT NULL, data_version VARCHAR(255) NOT NULL, match_id VARCHAR(255) NOT NULL, participants JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F143414E48FD905 ON metadata (game_id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C5D8BC1F8 FOREIGN KEY (info_id) REFERENCES info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CDC9EE959 FOREIGN KEY (metadata_id) REFERENCES metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE info ADD CONSTRAINT FK_CB893157E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metadata ADD CONSTRAINT FK_4F143414E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE info_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE metadata_id_seq CASCADE');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318C5D8BC1F8');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318CDC9EE959');
        $this->addSql('ALTER TABLE info DROP CONSTRAINT FK_CB893157E48FD905');
        $this->addSql('ALTER TABLE metadata DROP CONSTRAINT FK_4F143414E48FD905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE metadata');
    }
}
