<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_of_works')->insert([
            'work' => 'Монтажные работы'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Сантехнические работы'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Служба эксплуатации'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Уборка'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Сварочные работы'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Электрик'
        ]);
        DB::table('type_of_works')->insert([
            'work' => 'Техник'
        ]);
    }
}
