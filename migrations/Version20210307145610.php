<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210307145610 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file ADD beer_file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610F5056D9D FOREIGN KEY (beer_file_id) REFERENCES beer_file (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610F5056D9D ON file (beer_file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610F5056D9D');
        $this->addSql('DROP INDEX IDX_8C9F3610F5056D9D ON file');
        $this->addSql('ALTER TABLE file DROP beer_file_id');
    }
}
