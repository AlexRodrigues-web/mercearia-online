<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFuncionariosTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('funcionario');
        $table->addColumn('nome', 'string', ['limit' => 70])
              ->addColumn('cargo_id', 'integer')
              ->addColumn('nivel_id', 'integer')
              ->addColumn('credencial', 'string', ['limit' => 20, 'null' => false])
              ->addColumn('senha', 'string', ['limit' => 256, 'null' => false])
              ->addColumn('ativo', 'boolean', ['default' => 1])
              ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['credencial'], ['unique' => true]) // Garante credencial única
              ->addForeignKey('cargo_id', 'cargo', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addForeignKey('nivel_id', 'nivel', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->create();
    }
}
