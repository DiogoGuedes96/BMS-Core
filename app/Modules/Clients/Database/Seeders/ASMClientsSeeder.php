<?php

namespace App\Modules\Clients\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ASMClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmClientsModules();
        }
    }

    public function asmClientsModules()
    {
        $clients = [
            [
                "name" => "John Doe",
                "email" => "john@mail.com",
                "type" => "public",
                "address" => "Street number 1",
                "nif" => "123456789",
                "phone" => "911911922",
                "status" => 1,
            ],
            [
                "name" => "Alice Smith",
                "email" => "alice@mail.com",
                "type" => "private",
                "address" => "123 Elm Street",
                "nif" => "987654321",
                "phone" => "969786604",
                "status" => 1,
            ],
            [
                "name" => "Bob Johnson",
                "email" => "bob@mail.com",
                "type" => "public",
                "address" => "456 Oak Avenue",
                "nif" => "246813579",
                "phone" => "910066327",
                "status" => 1,
            ],
            [
                "name" => "Eva White",
                "email" => "eva@mail.com",
                "type" => "private",
                "address" => "789 Pine Road",
                "nif" => "135792468",
                "phone" => "955997788",
                "status" => 1,
            ],
            [
                "name" => "David Brown",
                "email" => "david@mail.com",
                "type" => "public",
                "address" => "101 Cedar Lane",
                "nif" => "111122223",
                "phone" => "964367459",
                "status" => 1,
            ],
            [
                "name" => "Grace Miller",
                "email" => "grace@mail.com",
                "type" => "private",
                "address" => "202 Willow Street",
                "nif" => "333444555",
                "phone" => "977444555",
                "status" => 1,
            ],
            [
                "name" => "Charlie Wilson",
                "email" => "charlie@mail.com",
                "type" => "public",
                "address" => "303 Birch Avenue",
                "nif" => "555444333",
                "phone" => "988777999",
                "status" => 1,
            ],
            [
                "name" => "Olivia Davis",
                "email" => "olivia@mail.com",
                "type" => "private",
                "address" => "404 Maple Road",
                "nif" => "777555222",
                "phone" => "966600166",
                "status" => 1,
            ],
            [
                "name" => "Daniel Lee",
                "email" => "daniel@mail.com",
                "type" => "public",
                "address" => "505 Cedar Lane",
                "nif" => "111000999",
                "phone" => "911990099",
                "status" => 1,
            ],
            [
                "name" => "Sophia Hall",
                "email" => "sophia@mail.com",
                "type" => "private",
                "address" => "606 Oak Avenue",
                "nif" => "444777222",
                "phone" => "977000777",
                "status" => 1,
            ]
        ];

        foreach ($clients as $client){
            if (!DB::table('clients')->where('email', $client['email'])->exists()) {
                DB::table('clients')->insert($client);
            }
        }
    }
}