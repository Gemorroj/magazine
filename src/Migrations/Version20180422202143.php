<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180422202143 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 1')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 2')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 3')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 4')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 5')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 6')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 7')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 8')");
        $this->addSql("INSERT INTO category (date_create, name) VALUES (DATETIME('now'), 'Категория 9')");


        $this->addSql("INSERT INTO product (category_id, date_create, name, description, price, size, composition, manufacturer) VALUES (1, DATETIME('now'), 'Костюм 1', 'Клевый костюм', 123.12, 'Большой', 'Шерсть и все такое', 'Китай')");
        $this->addSql("INSERT INTO product (category_id, date_create, name, description, price, size, composition, manufacturer) VALUES (1, DATETIME('now'), 'Костюм 2', 'Не Клевый костюм', 321.12, 'Маленький', 'Синтетика', 'Тоже Китай')");
        $this->addSql("INSERT INTO product (category_id, date_create, name, description, price, size, composition, manufacturer) VALUES (2, DATETIME('now'), 'Костюм 3', 'Еще костюм', 1.2, 'Средний', 'Хз', 'Конечно Китай')");

        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (1, DATETIME('now'), 'https://lorempixel.com/800/800/?1')");
        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (2, DATETIME('now'), 'https://lorempixel.com/800/800/?2')");
        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (2, DATETIME('now'), 'https://lorempixel.com/800/800/?3')");
        $this->addSql("INSERT INTO photo (product_id, date_create, path) VALUES (3, DATETIME('now'), 'https://lorempixel.com/800/800/?4')");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql("DELETE FROM photo");
        $this->addSql("DELETE FROM product");
        $this->addSql("DELETE FROM category");
        $this->addSql("VACUUM");
    }
}
