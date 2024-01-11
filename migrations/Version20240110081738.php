<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110081738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE voter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, election_id INT DEFAULT NULL, INDEX IDX_268C4A59A76ED395 (user_id), INDEX IDX_268C4A59A708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE voter ADD CONSTRAINT FK_268C4A59A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE voter ADD CONSTRAINT FK_268C4A59A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE vote ADD candidate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856491BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('CREATE INDEX IDX_5A10856491BD8781 ON vote (candidate_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voter DROP FOREIGN KEY FK_268C4A59A76ED395');
        $this->addSql('ALTER TABLE voter DROP FOREIGN KEY FK_268C4A59A708DAFF');
        $this->addSql('DROP TABLE voter');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856491BD8781');
        $this->addSql('DROP INDEX IDX_5A10856491BD8781 ON vote');
        $this->addSql('ALTER TABLE vote DROP candidate_id');
    }
}
