<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170401040152 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client_communication (client_id INT NOT NULL, communication_id INT NOT NULL, INDEX IDX_8E198FDF19EB6921 (client_id), INDEX IDX_8E198FDF1C2D1E0C (communication_id), PRIMARY KEY(client_id, communication_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe_communication (employe_id INT NOT NULL, communication_id INT NOT NULL, INDEX IDX_F420622D1B65292 (employe_id), INDEX IDX_F420622D1C2D1E0C (communication_id), PRIMARY KEY(employe_id, communication_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_cancellation (id INT AUTO_INCREMENT NOT NULL, communication_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_16DD57FF1C2D1E0C (communication_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, employe_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, working_days LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_5A3811FB1B65292 (employe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_block (id INT AUTO_INCREMENT NOT NULL, schedule_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_from DATETIME NOT NULL, date_to DATETIME NOT NULL, INDEX IDX_7F42577EA40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SCHEDULED_COMMAND (ID_SCHEDULED_COMMAND INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(100) NOT NULL, COMMAND VARCHAR(100) NOT NULL, ARGUMENTS VARCHAR(250) DEFAULT NULL, CRON_EXPRESSION VARCHAR(100) DEFAULT NULL, DH_LAST_EXECUTION DATETIME NOT NULL, LAST_RETURN_CODE INT DEFAULT NULL, LOG_FILE VARCHAR(100) DEFAULT NULL, PRIORITY INT NOT NULL, B_EXECUTE_IMMEDIATELY TINYINT(1) NOT NULL, B_DISABLED TINYINT(1) NOT NULL, B_LOCKED TINYINT(1) NOT NULL, PRIMARY KEY(ID_SCHEDULED_COMMAND)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_communication ADD CONSTRAINT FK_8E198FDF19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_communication ADD CONSTRAINT FK_8E198FDF1C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_communication ADD CONSTRAINT FK_F420622D1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_communication ADD CONSTRAINT FK_F420622D1C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_cancellation ADD CONSTRAINT FK_16DD57FF1C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE schedule_block ADD CONSTRAINT FK_7F42577EA40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('ALTER TABLE client ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455EE45BDBF FOREIGN KEY (picture_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455EE45BDBF ON client (picture_id)');
        $this->addSql('ALTER TABLE communication CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE dateSent dateSent DATETIME NOT NULL');
        $this->addSql('ALTER TABLE contact CHANGE phone_cell phone_cell VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', CHANGE phone_work phone_work VARCHAR(255) DEFAULT NULL, CHANGE phone_home phone_home VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE employe ADD user_id INT DEFAULT NULL, ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9EE45BDBF FOREIGN KEY (picture_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B9A76ED395 ON employe (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B9EE45BDBF ON employe (picture_id)');
        $this->addSql('ALTER TABLE event ADD event_cancellation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D5419431 FOREIGN KEY (event_cancellation_id) REFERENCES event_cancellation (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7D5419431 ON event (event_cancellation_id)');
        $this->addSql('ALTER TABLE event_reminder ADD communication_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_reminder ADD CONSTRAINT FK_6DBA6901C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6DBA6901C2D1E0C ON event_reminder (communication_id)');
        $this->addSql('ALTER TABLE user ADD reset_password_token VARCHAR(255) DEFAULT NULL, ADD reset_password_date DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D5419431');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455EE45BDBF');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9EE45BDBF');
        $this->addSql('ALTER TABLE schedule_block DROP FOREIGN KEY FK_7F42577EA40BC2D5');
        $this->addSql('DROP TABLE client_communication');
        $this->addSql('DROP TABLE employe_communication');
        $this->addSql('DROP TABLE event_cancellation');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE schedule_block');
        $this->addSql('DROP TABLE SCHEDULED_COMMAND');
        $this->addSql('DROP INDEX UNIQ_C7440455EE45BDBF ON client');
        $this->addSql('ALTER TABLE client DROP picture_id');
        $this->addSql('ALTER TABLE communication CHANGE phone phone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE dateSent dateSent VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE contact CHANGE phone_cell phone_cell VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE phone_work phone_work VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE phone_home phone_home VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9A76ED395');
        $this->addSql('DROP INDEX UNIQ_F804D3B9A76ED395 ON employe');
        $this->addSql('DROP INDEX UNIQ_F804D3B9EE45BDBF ON employe');
        $this->addSql('ALTER TABLE employe DROP user_id, DROP picture_id');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7D5419431 ON event');
        $this->addSql('ALTER TABLE event DROP event_cancellation_id');
        $this->addSql('ALTER TABLE event_reminder DROP FOREIGN KEY FK_6DBA6901C2D1E0C');
        $this->addSql('DROP INDEX UNIQ_6DBA6901C2D1E0C ON event_reminder');
        $this->addSql('ALTER TABLE event_reminder DROP communication_id');
        $this->addSql('ALTER TABLE user DROP reset_password_token, DROP reset_password_date');
    }
}
