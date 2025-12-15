<?php

namespace App\Libraries;

use Config\Database;
use CodeIgniter\I18n\Time;

class DatabaseInitializer
{
    private static function create_users_table($db, $forge)
    {
        if (!$db->tableExists('users')) {
            $fields = [
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'uuid' => [
                    'type'       => 'CHAR',
                    'constraint' => '36',
                    'unique'     => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'unique'     => true,
                ],
                'password' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'image' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'user_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['admin', 'user'],
                    'default'    => 'user',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];

            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->createTable('users', true);
        }
    }

    private static function insert_initial_data($db)
    {
        $builder = $db->table('users');
        $count = $builder->countAllResults();

        if ($count === 0) {
            $data = [
                'uuid' => generate_uuid(),
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'image' => 'default-user-image.webp',
                'user_type' => 'admin',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $builder->insert($data);
        }
    }

    public static function initialize()
    {
        $db = Database::connect();
        $forge = \Config\Database::forge();

        self::create_users_table($db, $forge);
        self::insert_initial_data($db);
    }
}
