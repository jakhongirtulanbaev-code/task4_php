<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241203170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table with unique index on email';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `user` (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT \'unverified\',
            confirmation_token VARCHAR(64) DEFAULT NULL,
            registered_at DATETIME NOT NULL,
            last_login_at DATETIME DEFAULT NULL,
            last_activity_at DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        // Unique index requirement
        $this->addSql('CREATE UNIQUE INDEX idx_users_email_unique ON `user`(email)');
        $this->addSql('CREATE INDEX idx_users_status ON `user`(status)');
        $this->addSql('CREATE INDEX idx_users_last_login ON `user`(last_login_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
    }
}

