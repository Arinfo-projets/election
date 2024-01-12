<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112091415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD until_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE is_open is_open TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election DROP created_at, DROP until_at, CHANGE is_open is_open TINYINT(1) DEFAULT NULL');
    }
}
