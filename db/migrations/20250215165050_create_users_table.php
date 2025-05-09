<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('users');
        $table->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('email', 'string', ['limit' => 150])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}