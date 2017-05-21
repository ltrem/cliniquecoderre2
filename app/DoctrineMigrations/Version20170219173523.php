<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170219173523 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employe_communication (employe_id INT NOT NULL, communication_id INT NOT NULL, INDEX IDX_F420622D1B65292 (employe_id), INDEX IDX_F420622D1C2D1E0C (communication_id), PRIMARY KEY(employe_id, communication_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employe_communication ADD CONSTRAINT FK_F420622D1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_communication ADD CONSTRAINT FK_F420622D1C2D1E0C FOREIGN KEY (communication_id) REFERENCES communication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9EE45BDBF FOREIGN KEY (picture_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B9EE45BDBF ON employe (picture_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE employe_communication');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_F804D3B9EE45BDBF ON employe');
        $this->addSql('ALTER TABLE employe DROP picture_id');
    }
}
