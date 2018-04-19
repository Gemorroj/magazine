<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418090000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 1')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 2')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 3')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 4')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 5')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 6')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 7')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 8')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (NOW(), 'Категория 9')");


        $this->addSql("INSERT INTO product (category_id, date_create, name, description, price, size, composition, manufacturer) VALUES (1, NOW(), 'Костюм 1', 'Клевый костюм', 123.12, 'Большой', 'Шерсть и все такое', 'Китай')");
        $this->addSql("INSERT INTO product (category_id, date_create, name, description, price, size, composition, manufacturer) VALUES (1, NOW(), 'Костюм 2', 'Не Клевый костюм', 321.12, 'Маленький', 'Синтетика', 'Тоже Китай')");

        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (1, NOW(), 'https://lorempixel.com/800/800/?1')");
        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (2, NOW(), 'https://lorempixel.com/800/800/?2')");
        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (2, NOW(), 'https://lorempixel.com/800/800/?3')");
    }

    public function down(Schema $schema)
    {
        $this->addSql("TRUNCATE TABLE photo");
        $this->addSql("TRUNCATE TABLE product");
        $this->addSql("TRUNCATE TABLE category");
    }
}
