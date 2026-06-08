<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000002 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO users (email, password, name, roles) VALUES
            ('admin@insurance.test', '\$2y\$12\$gDvHiHoIz6tA92B/EOVx0O1H0xyeS9/.Ew5qPg706mpw86DuuAdWC', 'Admin User', '[\"ROLE_USER\",\"ROLE_ADMIN\"]'),
            ('agent@insurance.test', '\$2y\$12\$gDvHiHoIz6tA92B/EOVx0O1H0xyeS9/.Ew5qPg706mpw86DuuAdWC', 'Agent User', '[\"ROLE_USER\"]')
        ");

        $this->addSql("INSERT INTO policies (user_id, policy_number, insured_name, insured_document, risk_type, base_premium_cents, premium_cents, status, starts_at, expires_at, created_at) VALUES
            (1, 'POL-DEMO0001', 'Carlos García López', '12345678A', 'low',    50000,  50000, 'active',    '2024-01-01 00:00:00', '2025-01-01 00:00:00', NOW()),
            (1, 'POL-DEMO0002', 'María Fernández Ruiz', '87654321B', 'medium', 50000,  75000, 'active',    '2024-03-01 00:00:00', '2025-03-01 00:00:00', NOW()),
            (2, 'POL-DEMO0003', 'Juan Martínez Sanz', '11223344C', 'high',   50000, 110000, 'draft',     '2024-06-01 00:00:00', '2025-06-01 00:00:00', NOW()),
            (1, 'POL-DEMO0004', 'Ana Torres Vega', '44332211D', 'low',    80000,  80000, 'suspended', '2023-01-01 00:00:00', '2024-01-01 00:00:00', NOW())
        ");

        $this->addSql("INSERT INTO claims (policy_id, user_id, claim_number, description, claimed_amount_cents, approved_amount_cents, status, rejection_reason, occurred_at, created_at) VALUES
            (1, 1, 'CLM-DEMO0001', 'Accidente de tráfico en autopista A-6', 250000, 200000, 'approved', NULL,                          '2024-02-15 00:00:00', NOW()),
            (2, 1, 'CLM-DEMO0002', 'Robo del vehículo en parking',          500000, NULL,   'reported', NULL,                          '2024-04-10 00:00:00', NOW()),
            (1, 1, 'CLM-DEMO0003', 'Daños por granizo en carrocería',        80000, NULL,   'rejected', 'Daños previos no declarados', '2024-03-20 00:00:00', NOW())
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM claims');
        $this->addSql('DELETE FROM policies');
        $this->addSql('DELETE FROM users');
    }
}
