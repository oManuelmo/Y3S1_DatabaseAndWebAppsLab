<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $databasePath = base_path('database/database.sql');
        $populatePath = base_path('database/populate.sql');
        $databaseSql = file_get_contents($databasePath);
        DB::unprepared($databaseSql);
        $this->command->info('Database seeded!');
        $populateSql = file_get_contents($populatePath);
        DB::unprepared($populateSql);
        $this->command->info('Populate seeded!');
    }
}
