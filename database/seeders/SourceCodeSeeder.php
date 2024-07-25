<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SourceCode;

class SourceCodeSeeder extends Seeder
{
    public function run()
    {
        SourceCode::factory()->count(50)->create(); // Adjust the number as needed
    }
}
