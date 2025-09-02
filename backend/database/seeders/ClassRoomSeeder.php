<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classRooms = [
            // Kelas 7
            ['name' => '7A', 'level' => '7', 'description' => 'Kelas 7A'],
            ['name' => '7B', 'level' => '7', 'description' => 'Kelas 7B'],
            ['name' => '7C', 'level' => '7', 'description' => 'Kelas 7C'],
            ['name' => '7D', 'level' => '7', 'description' => 'Kelas 7D'],
            
            // Kelas 8
            ['name' => '8A', 'level' => '8', 'description' => 'Kelas 8A'],
            ['name' => '8B', 'level' => '8', 'description' => 'Kelas 8B'],
            ['name' => '8C', 'level' => '8', 'description' => 'Kelas 8C'],
            ['name' => '8D', 'level' => '8', 'description' => 'Kelas 8D'],
            
            // Kelas 9
            ['name' => '9A', 'level' => '9', 'description' => 'Kelas 9A'],
            ['name' => '9B', 'level' => '9', 'description' => 'Kelas 9B'],
            ['name' => '9C', 'level' => '9', 'description' => 'Kelas 9C'],
            ['name' => '9D', 'level' => '9', 'description' => 'Kelas 9D'],
        ];

        foreach ($classRooms as $classRoom) {
            ClassRoom::create($classRoom);
        }
    }
}
