<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdicionarTabelasFaltantes extends AbstractMigration
{
    public function change(): void
    {
        // Criar tabela caixa (somente se não existir)
        if (!$this->hasTable('caixa')) {
            $table = $this->table('caixa');
            $table->addColumn('saldo_inicial', 'decimal', ['precision' => 10, 'scale' => 2])
                  ->addColumn('saldo_final', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
                  ->addColumn('dt_abertura', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addColumn('dt_fechamento', 'datetime', ['null' => true])
                  ->create();
        }

        // Criar tabela cargo
        if (!$this->hasTable('cargo')) {
            $table = $this->table('cargo');
            $table->addColumn('nome', 'string', ['limit' => 50])
                  ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->create();
        }

        // Criar tabela nivel
        if (!$this->hasTable('nivel')) {
            $table = $this->table('nivel');
            $table->addColumn('nome', 'string', ['limit' => 50])
                  ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->create();
        }

        // Criar tabela estoque
        if (!$this->hasTable('estoque')) {
            $table = $this->table('estoque');
            $table->addColumn('produto_id', 'integer')
                  ->addColumn('quantidade', 'integer')
                  ->addColumn('dt_atualizacao', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addForeignKey('produto_id', 'produto', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->create();
        }

        // Criar tabela usuario_pg_privada
        if (!$this->hasTable('usuario_pg_privada')) {
            $table = $this->table('usuario_pg_privada');
            $table->addColumn('usuario_id', 'integer')
                  ->addColumn('pg_privada_id', 'integer')
                  ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addForeignKey('usuario_id', 'usuarios', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->addForeignKey('pg_privada_id', 'pg_privada', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->create();
        }

        // Criar tabela funcionario_pg_privada
        if (!$this->hasTable('funcionario_pg_privada')) {
            $table = $this->table('funcionario_pg_privada');
            $table->addColumn('funcionario_id', 'integer')
                  ->addColumn('pg_privada_id', 'integer')
                  ->addColumn('dt_registro', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
                  ->addForeignKey('funcionario_id', 'funcionario', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->addForeignKey('pg_privada_id', 'pg_privada', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->create();
        }
    }
}
