<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run(): void  
    {
        $emails = ["admin@email.com", "user@email.com"];

        foreach ($emails as $email) {
            $exists = $this->fetchRow("SELECT id FROM usuarios WHERE email = '$email'");

            if (!$exists) {
                $data[] = [
                    'nome'       => ($email === 'admin@email.com') ? 'Admin' : 'User Test',
                    'email'      => $email,
                    'senha'      => password_hash(($email === 'admin@email.com') ? 'admin123' : 'user123', PASSWORD_DEFAULT),
                    'dt_registro'=> date('Y-m-d H:i:s')
                ];
            }
        }

        if (!empty($data)) {
            $this->table('usuarios')
                ->insert($data)
                ->saveData();
        }
    }
}

