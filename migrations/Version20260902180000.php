<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Catalog products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog_products (
            id VARCHAR(36) NOT NULL,
            type VARCHAR(32) NOT NULL,
            title VARCHAR(255) NOT NULL,
            ean VARCHAR(13) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            year SMALLINT DEFAULT NULL,
            weight INT DEFAULT NULL,
            length INT DEFAULT NULL,
            width INT DEFAULT NULL,
            height INT DEFAULT NULL,
            listing_status VARCHAR(32) NOT NULL,
            list_price_amount INT NOT NULL,
            list_price_currency VARCHAR(3) NOT NULL,
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE catalog_products');
    }
}
