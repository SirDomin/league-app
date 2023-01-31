<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131112503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_2b783e37f83c0411');
        $this->addSql('CREATE INDEX IDX_2B783E37F83C0411 ON perks (perk_style_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_2B783E37F83C0411');
        $this->addSql('CREATE UNIQUE INDEX uniq_2b783e37f83c0411 ON perks (perk_style_id)');
    }
}
