<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170305040516 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_block ADD schedule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule_block ADD CONSTRAINT FK_7F42577EA40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('CREATE INDEX IDX_7F42577EA40BC2D5 ON schedule_block (schedule_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_block DROP FOREIGN KEY FK_7F42577EA40BC2D5');
        $this->addSql('DROP INDEX IDX_7F42577EA40BC2D5 ON schedule_block');
        $this->addSql('ALTER TABLE schedule_block DROP schedule_id');
    }
}
