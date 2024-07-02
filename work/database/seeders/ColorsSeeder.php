<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('colors')->truncate();
      DB::table('colors')->insert([
        ['english'=>'red','japanese'=>'あか', 'created_at'=>now()],
        ['english'=>'blue','japanese'=>'あお', 'created_at'=>now()],
        ['english'=>'yellow','japanese'=>'きい', 'created_at'=>now()],
      ]);
    }
}
