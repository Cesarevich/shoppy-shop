<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261010185732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Catalog product categories table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog_product_categories (
            product_id VARCHAR(36) NOT NULL,
            category_id VARCHAR(36) NOT NULL,
            PRIMARY KEY (product_id)
        )');
        $this->addSql('ALTER TABLE catalog_product_categories ADD CONSTRAINT fk_catalog_product_categories_product_id FOREIGN KEY (product_id) REFERENCES catalog_products (id)');
        $this->addSql('ALTER TABLE catalog_product_categories ADD CONSTRAINT fk_catalog_product_categories_category_id FOREIGN KEY (category_id) REFERENCES catalog_categories (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE catalog_product_categories DROP CONSTRAINT fk_catalog_product_categories_product_id');
        $this->addSql('ALTER TABLE catalog_product_categories DROP CONSTRAINT fk_catalog_product_categories_category_id');
        $this->addSql('DROP TABLE catalog_product_categories');
    }
}
