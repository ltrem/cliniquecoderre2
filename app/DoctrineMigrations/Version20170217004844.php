<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170217004844 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE SCHEDULED_COMMAND (ID_SCHEDULED_COMMAND INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(100) NOT NULL, COMMAND VARCHAR(100) NOT NULL, ARGUMENTS VARCHAR(250) DEFAULT NULL, CRON_EXPRESSION VARCHAR(100) DEFAULT NULL, DH_LAST_EXECUTION DATETIME NOT NULL, LAST_RETURN_CODE INT DEFAULT NULL, LOG_FILE VARCHAR(100) DEFAULT NULL, PRIORITY INT NOT NULL, B_EXECUTE_IMMEDIATELY TINYINT(1) NOT NULL, B_DISABLED TINYINT(1) NOT NULL, B_LOCKED TINYINT(1) NOT NULL, PRIMARY KEY(ID_SCHEDULED_COMMAND)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE SCHEDULED_COMMAND');
    }
}
