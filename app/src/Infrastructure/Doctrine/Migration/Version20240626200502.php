<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240626200502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates "Log" table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE log (
            id INT AUTO_INCREMENT NOT NULL, 
            service_name VARCHAR(255) NOT NULL, 
            code VARCHAR(255) NOT NULL, 
            date_time DATETIME NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE log');
    }
}
