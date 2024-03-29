<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328195052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quantity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ingredient (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE quantity (id INT NOT NULL, recipe_id INT NOT NULL, ingredient_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9FF3163659D8A214 ON quantity (recipe_id)');
        $this->addSql('CREATE INDEX IDX_9FF31636933FE08C ON quantity (ingredient_id)');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF3163659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF31636933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quantity_id_seq CASCADE');
        $this->addSql('ALTER TABLE quantity DROP CONSTRAINT FK_9FF3163659D8A214');
        $this->addSql('ALTER TABLE quantity DROP CONSTRAINT FK_9FF31636933FE08C');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE quantity');
    }
}
