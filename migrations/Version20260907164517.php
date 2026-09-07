<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260907164517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Catalog product types table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog_product_types (
            id VARCHAR(36) NOT NULL,
            code VARCHAR(32) NOT NULL,
            title VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_catalog_product_types_code ON catalog_product_types (code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE catalog_product_types');
    }
}
