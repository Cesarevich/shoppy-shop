<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260907190500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Product references Type by type_id instead of a string enum';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM catalog_products');
        $this->addSql('ALTER TABLE catalog_products DROP COLUMN type');
        $this->addSql('ALTER TABLE catalog_products ADD type_id VARCHAR(36) NOT NULL');
        $this->addSql('CREATE INDEX idx_catalog_products_type_id ON catalog_products (type_id)');
        $this->addSql('ALTER TABLE catalog_products ADD CONSTRAINT fk_catalog_products_type_id FOREIGN KEY (type_id) REFERENCES catalog_product_types (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE catalog_products DROP CONSTRAINT fk_catalog_products_type_id');
        $this->addSql('DROP INDEX idx_catalog_products_type_id');
        $this->addSql('ALTER TABLE catalog_products DROP COLUMN type_id');
        $this->addSql('ALTER TABLE catalog_products ADD type VARCHAR(32) NOT NULL');
    }
}
