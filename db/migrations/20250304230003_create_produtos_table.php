<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProdutosTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('produto', ['encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('nome', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('preco', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false])
              ->addColumn('fornecedor_id', 'integer', ['null' => false])
              ->addColumn('kilograma', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
              ->addColumn('litro', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
              ->addColumn('codigo_id', 'integer', ['null' => false])
              ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('fornecedor_id', 'fornecedor', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addForeignKey('codigo_id', 'codigo', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->create();
    }
}
