<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180422202142 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE category (id INTEGER NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE photo (id INTEGER NOT NULL, product_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14B784184584665A ON photo (product_id)');
        $this->addSql('CREATE TABLE product (id INTEGER NOT NULL, category_id INTEGER UNSIGNED NOT NULL, date_create DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, price NUMERIC(10, 2) NOT NULL, size VARCHAR(255) DEFAULT NULL, composition VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE product');
    }
}
