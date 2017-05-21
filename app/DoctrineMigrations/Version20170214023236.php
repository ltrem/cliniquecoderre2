<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214023236 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client ADD picture_id INT DEFAULT NULL, DROP image_name');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455EE45BDBF FOREIGN KEY (picture_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455EE45BDBF ON client (picture_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_C7440455EE45BDBF ON client');
        $this->addSql('ALTER TABLE client ADD image_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP picture_id');
    }
}
