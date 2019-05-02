<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190501094229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_8C9F36108F93B6FC');
        $this->addSql('DROP INDEX IDX_8C9F361012A1C43A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, movie_id, resolution_id, name, path, size, subs, uncensored FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, movie_id INTEGER DEFAULT NULL, resolution_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, path CLOB NOT NULL COLLATE BINARY, size BIGINT DEFAULT NULL, subs BOOLEAN NOT NULL, uncensored BOOLEAN NOT NULL, CONSTRAINT FK_8C9F36108F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8C9F361012A1C43A FOREIGN KEY (resolution_id) REFERENCES resolutions (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO file (id, movie_id, resolution_id, name, path, size, subs, uncensored) SELECT id, movie_id, resolution_id, name, path, size, subs, uncensored FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F36108F93B6FC ON file (movie_id)');
        $this->addSql('CREATE INDEX IDX_8C9F361012A1C43A ON file (resolution_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__scan AS SELECT id, path, timestamp FROM scan');
        $this->addSql('DROP TABLE scan');
        $this->addSql('CREATE TABLE scan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, path CLOB NOT NULL COLLATE BINARY, scanned_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO scan (id, path, scanned_at) SELECT id, path, timestamp FROM __temp__scan');
        $this->addSql('DROP TABLE __temp__scan');
        $this->addSql('DROP INDEX IDX_79E4D48B2827AAD3');
        $this->addSql('DROP INDEX IDX_79E4D48B93CB796C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__scan_file AS SELECT scan_id, file_id FROM scan_file');
        $this->addSql('DROP TABLE scan_file');
        $this->addSql('CREATE TABLE scan_file (scan_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(scan_id, file_id), CONSTRAINT FK_79E4D48B2827AAD3 FOREIGN KEY (scan_id) REFERENCES scan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_79E4D48B93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO scan_file (scan_id, file_id) SELECT scan_id, file_id FROM __temp__scan_file');
        $this->addSql('DROP TABLE __temp__scan_file');
        $this->addSql('CREATE INDEX IDX_79E4D48B2827AAD3 ON scan_file (scan_id)');
        $this->addSql('CREATE INDEX IDX_79E4D48B93CB796C ON scan_file (file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_8C9F36108F93B6FC');
        $this->addSql('DROP INDEX IDX_8C9F361012A1C43A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, movie_id, resolution_id, name, path, size, subs, uncensored FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, movie_id INTEGER DEFAULT NULL, resolution_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, path CLOB NOT NULL, size BIGINT DEFAULT NULL, subs BOOLEAN NOT NULL, uncensored BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO file (id, movie_id, resolution_id, name, path, size, subs, uncensored) SELECT id, movie_id, resolution_id, name, path, size, subs, uncensored FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F36108F93B6FC ON file (movie_id)');
        $this->addSql('CREATE INDEX IDX_8C9F361012A1C43A ON file (resolution_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__scan AS SELECT id, path, scanned_at FROM scan');
        $this->addSql('DROP TABLE scan');
        $this->addSql('CREATE TABLE scan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, path CLOB NOT NULL, timestamp DATETIME NOT NULL)');
        $this->addSql('INSERT INTO scan (id, path, timestamp) SELECT id, path, scanned_at FROM __temp__scan');
        $this->addSql('DROP TABLE __temp__scan');
        $this->addSql('DROP INDEX IDX_79E4D48B2827AAD3');
        $this->addSql('DROP INDEX IDX_79E4D48B93CB796C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__scan_file AS SELECT scan_id, file_id FROM scan_file');
        $this->addSql('DROP TABLE scan_file');
        $this->addSql('CREATE TABLE scan_file (scan_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(scan_id, file_id))');
        $this->addSql('INSERT INTO scan_file (scan_id, file_id) SELECT scan_id, file_id FROM __temp__scan_file');
        $this->addSql('DROP TABLE __temp__scan_file');
        $this->addSql('CREATE INDEX IDX_79E4D48B2827AAD3 ON scan_file (scan_id)');
        $this->addSql('CREATE INDEX IDX_79E4D48B93CB796C ON scan_file (file_id)');
    }
}
