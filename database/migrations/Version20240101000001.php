<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            roles JSON NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE policies (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            policy_number VARCHAR(50) NOT NULL UNIQUE,
            insured_name VARCHAR(255) NOT NULL,
            insured_document VARCHAR(50) NOT NULL,
            risk_type VARCHAR(20) NOT NULL,
            base_premium_cents INT NOT NULL,
            premium_cents INT NOT NULL,
            status VARCHAR(20) NOT NULL,
            starts_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE claims (
            id INT AUTO_INCREMENT NOT NULL,
            policy_id INT NOT NULL,
            user_id INT NOT NULL,
            claim_number VARCHAR(50) NOT NULL UNIQUE,
            description TEXT NOT NULL,
            claimed_amount_cents INT NOT NULL,
            approved_amount_cents INT DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            rejection_reason VARCHAR(500) DEFAULT NULL,
            occurred_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE claims');
        $this->addSql('DROP TABLE policies');
        $this->addSql('DROP TABLE users');
    }
}
