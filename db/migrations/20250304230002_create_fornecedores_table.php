<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFornecedoresTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fornecedor');
        $table->addColumn('nome', 'string', ['limit' => 50, 'null' => false])
              ->addColumn('nipc', 'string', ['limit' => 15, 'null' => false])
              ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['nipc'], ['unique' => true]) // Garante que o NIPC seja único
              ->create();
    }
}
