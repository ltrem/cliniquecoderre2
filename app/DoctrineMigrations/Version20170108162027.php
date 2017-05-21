<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170108162027 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coordinate DROP FOREIGN KEY FK_CB9CBA171B65292');
        $this->addSql('DROP INDEX IDX_CB9CBA171B65292 ON coordinate');
        $this->addSql('ALTER TABLE coordinate DROP employe_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coordinate ADD employe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coordinate ADD CONSTRAINT FK_CB9CBA171B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('CREATE INDEX IDX_CB9CBA171B65292 ON coordinate (employe_id)');
    }
}
