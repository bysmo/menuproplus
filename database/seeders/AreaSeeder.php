<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($branch): void
    {
        $areas = [
            [
                'area_name' => 'Salle',
                'branch_id' => $branch->id,
            ],
            [
                'area_name' => 'Terrasse',
                'branch_id' => $branch->id,
            ],
            [
                'area_name' => 'Jardin',
                'branch_id' => $branch->id,
            ]
        ];

        Area::insert($areas);
    }
}
