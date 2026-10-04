<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261004152500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Catalog product history table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog_product_history (
            id VARCHAR(36) NOT NULL,
            product_id VARCHAR(36) NOT NULL,
            event_name VARCHAR(64) NOT NULL,
            occurred_on VARCHAR(40) NOT NULL,
            changes JSON NOT NULL,
            PRIMARY KEY (id)
        )');
        $this->addSql('CREATE INDEX idx_catalog_product_history_product_id ON catalog_product_history (product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE catalog_product_history');
    }
}
