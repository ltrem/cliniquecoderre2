<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170122184035 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_reminder ADD communication_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_reminder ADD CONSTRAINT FK_6DBA6901C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6DBA6901C2D1E0C ON event_reminder (communication_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_reminder DROP FOREIGN KEY FK_6DBA6901C2D1E0C');
        $this->addSql('DROP INDEX UNIQ_6DBA6901C2D1E0C ON event_reminder');
        $this->addSql('ALTER TABLE event_reminder DROP communication_id');
    }
}
