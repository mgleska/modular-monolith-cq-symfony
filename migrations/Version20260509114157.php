<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509114157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ofr_offer (id INT AUTO_INCREMENT NOT NULL, version INT DEFAULT 0 NOT NULL, store_id INT NOT NULL, external_id VARCHAR(50) NOT NULL, product_ean VARCHAR(48) NOT NULL, product_name VARCHAR(200) DEFAULT NULL, price INT NOT NULL, lowest_price INT DEFAULT NULL, visible TINYINT DEFAULT 1 NOT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1A8CF8E4B092A811879E927F (store_id, product_ean), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ofr_offer');
    }
}
