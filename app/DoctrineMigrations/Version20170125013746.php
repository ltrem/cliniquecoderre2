<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170125013746 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event ADD event_cancellation_id INT DEFAULT NULL, DROP cancelled');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D5419431 FOREIGN KEY (event_cancellation_id) REFERENCES event_cancellation (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7D5419431 ON event (event_cancellation_id)');
        $this->addSql('ALTER TABLE event_cancellation DROP FOREIGN KEY FK_16DD57FF71F7E88B');
        $this->addSql('DROP INDEX UNIQ_16DD57FF71F7E88B ON event_cancellation');
        $this->addSql('ALTER TABLE event_cancellation DROP event_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D5419431');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7D5419431 ON event');
        $this->addSql('ALTER TABLE event ADD cancelled TINYINT(1) NOT NULL, DROP event_cancellation_id');
        $this->addSql('ALTER TABLE event_cancellation ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_cancellation ADD CONSTRAINT FK_16DD57FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16DD57FF71F7E88B ON event_cancellation (event_id)');
    }
}
