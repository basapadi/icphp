<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = File::files(database_path('data/views'));

        foreach ($files as $file) {
            $viewName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $select   = File::get($file->getPathname());

            $view = "v_{$viewName}";

            DB::unprepared("
                DROP VIEW IF EXISTS {$view};
            ");

            DB::unprepared("
                CREATE VIEW {$view} AS
                {$select}
            ");
        }
    }
}
