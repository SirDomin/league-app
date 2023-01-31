<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131135748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE info ADD game_uuid BIGINT NOT NULL');
        $this->addSql('ALTER TABLE info ALTER game_id TYPE INT');
        $this->addSql('ALTER TABLE info ALTER game_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE info DROP game_uuid');
        $this->addSql('ALTER TABLE info ALTER game_id TYPE BIGINT');
        $this->addSql('ALTER TABLE info ALTER game_id SET NOT NULL');
    }
}
