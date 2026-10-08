<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261006202100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Catalog categories table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog_categories (
            id VARCHAR(36) NOT NULL,
            type_id VARCHAR(36) NOT NULL,
            title VARCHAR(255) NOT NULL,
            parent_id VARCHAR(36) DEFAULT NULL,
            PRIMARY KEY (id)
        )');
        $this->addSql('CREATE INDEX idx_catalog_categories_type_id ON catalog_categories (type_id)');
        $this->addSql('CREATE INDEX idx_catalog_categories_parent_id ON catalog_categories (parent_id)');
        $this->addSql('ALTER TABLE catalog_categories ADD CONSTRAINT fk_catalog_categories_type_id FOREIGN KEY (type_id) REFERENCES catalog_product_types (id)');
        $this->addSql('ALTER TABLE catalog_categories ADD CONSTRAINT fk_catalog_categories_parent_id FOREIGN KEY (parent_id) REFERENCES catalog_categories (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE catalog_categories DROP CONSTRAINT fk_catalog_categories_parent_id');
        $this->addSql('ALTER TABLE catalog_categories DROP CONSTRAINT fk_catalog_categories_type_id');
        $this->addSql('DROP TABLE catalog_categories');
    }
}
