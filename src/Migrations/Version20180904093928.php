<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180904093928 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_cart (id INT AUTO_INCREMENT NOT NULL, total INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, shopping_cart_id INT NOT NULL, name VARCHAR(24) NOT NULL, surname VARCHAR(64) NOT NULL, address LONGTEXT NOT NULL, gender INT NOT NULL, email LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_8D93D64945F80CD (shopping_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE to_buy (id INT AUTO_INCREMENT NOT NULL, shopping_cart_id INT NOT NULL, product_id INT NOT NULL, qty INT NOT NULL, INDEX IDX_99BF4A7945F80CD (shopping_cart_id), INDEX IDX_99BF4A794584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64945F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id)');
        $this->addSql('ALTER TABLE to_buy ADD CONSTRAINT FK_99BF4A7945F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id)');
        $this->addSql('ALTER TABLE to_buy ADD CONSTRAINT FK_99BF4A794584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE to_buy DROP FOREIGN KEY FK_99BF4A794584665A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64945F80CD');
        $this->addSql('ALTER TABLE to_buy DROP FOREIGN KEY FK_99BF4A7945F80CD');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE shopping_cart');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE to_buy');
    }
}
