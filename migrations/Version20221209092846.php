<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221209092846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo AS SELECT id, product_id, date_create, date_update, path FROM photo');
        $this->addSql('DROP TABLE photo');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, path VARCHAR(255) NOT NULL, CONSTRAINT FK_14B784184584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO photo (id, product_id, date_create, date_update, path) SELECT id, product_id, date_create, date_update, path FROM __temp__photo');
        $this->addSql('DROP TABLE __temp__photo');
        $this->addSql('CREATE INDEX IDX_14B784184584665A ON photo (product_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14B78418B548B0F ON photo (path)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, price NUMERIC(10, 2) NOT NULL, size VARCHAR(255) DEFAULT NULL, composition VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product (id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer) SELECT id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5E237E06 ON product (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo AS SELECT id, product_id, date_create, date_update, path FROM photo');
        $this->addSql('DROP TABLE photo');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, path VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO photo (id, product_id, date_create, date_update, path) SELECT id, product_id, date_create, date_update, path FROM __temp__photo');
        $this->addSql('DROP TABLE __temp__photo');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14B78418B548B0F ON photo (path)');
        $this->addSql('CREATE INDEX IDX_14B784184584665A ON photo (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, price NUMERIC(10, 2) NOT NULL, size VARCHAR(255) DEFAULT NULL, composition VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO product (id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer) SELECT id, category_id, date_create, date_update, name, description, price, size, composition, manufacturer FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5E237E06 ON product (name)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }
}
