<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170123004650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event_cancellation (id INT AUTO_INCREMENT NOT NULL, communication_id INT DEFAULT NULL, event_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_16DD57FF1C2D1E0C (communication_id), UNIQUE INDEX UNIQ_16DD57FF71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_cancellation ADD CONSTRAINT FK_16DD57FF1C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id)');
        $this->addSql('ALTER TABLE event_cancellation ADD CONSTRAINT FK_16DD57FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE event_cancellation');
    }
}
