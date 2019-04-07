<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190407131710 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, region_id_id INT NOT NULL, courier_id_id INT NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, INDEX IDX_7656F53BC7209D4F (region_id_id), INDEX IDX_7656F53BCCC02335 (courier_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, trip_days_to INT NOT NULL, trip_days_from INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE courier (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BC7209D4F FOREIGN KEY (region_id_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BCCC02335 FOREIGN KEY (courier_id_id) REFERENCES courier (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BC7209D4F');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BCCC02335');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE courier');
    }
}
