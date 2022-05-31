<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220531150752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paid_articles ADD deleted_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE settings ADD deleted_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transactions ADD deleted_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_wallet ADD deleted_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paid_articles DROP deleted_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE settings DROP deleted_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE transactions DROP deleted_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user_wallet DROP deleted_at, DROP created_at, DROP updated_at');
    }
}
