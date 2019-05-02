<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190501081732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, eng_name VARCHAR(512) DEFAULT NULL)');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, movie_id INTEGER DEFAULT NULL, resolution_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, path CLOB NOT NULL, size BIGINT DEFAULT NULL, subs BOOLEAN NOT NULL, uncensored BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8C9F36108F93B6FC ON file (movie_id)');
        $this->addSql('CREATE INDEX IDX_8C9F361012A1C43A ON file (resolution_id)');
        $this->addSql('CREATE TABLE resolutions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE scan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, path CLOB NOT NULL, timestamp DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE scan_file (scan_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(scan_id, file_id))');
        $this->addSql('CREATE INDEX IDX_79E4D48B2827AAD3 ON scan_file (scan_id)');
        $this->addSql('CREATE INDEX IDX_79E4D48B93CB796C ON scan_file (file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE resolutions');
        $this->addSql('DROP TABLE scan');
        $this->addSql('DROP TABLE scan_file');
    }
}
