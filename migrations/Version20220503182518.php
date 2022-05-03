<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503182518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX index_leave ON attendances');
        $this->addSql('DROP INDEX index_enter ON attendances');
        $this->addSql('CREATE INDEX index_enter ON attendances (entered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX index_enter ON attendances');
        $this->addSql('CREATE INDEX index_leave ON attendances (id, left_at)');
        $this->addSql('CREATE INDEX index_enter ON attendances (id, entered_at)');
    }
}
