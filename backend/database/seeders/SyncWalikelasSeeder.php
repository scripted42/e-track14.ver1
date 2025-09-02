<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassRoom;

class SyncWalikelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sync walikelas assignments
        $users = User::where('is_walikelas', true)->whereNotNull('class_room_id')->get();
        
        foreach ($users as $user) {
            $classRoom = ClassRoom::find($user->class_room_id);
            if ($classRoom && !$classRoom->walikelas_id) {
                $classRoom->update(['walikelas_id' => $user->id]);
                echo "Synced user {$user->name} to class {$classRoom->name}\n";
            }
        }
        
        echo "Walikelas sync completed!\n";
    }
}
