<?php

namespace Database\Seeders;

use Database\Factories\ToDoFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToDoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ToDoFactory::new()->count(3)->create();
    }
}
