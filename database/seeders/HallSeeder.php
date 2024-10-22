<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $halls =
        [
            [
                'descripcion'         => 'PRIMER PISO'
            ],
            [
                'descripcion'         => 'SEGUNDO PISO'
            ],
            [
                'descripcion'         => 'TERCER PISO'
            ]
        ];

        foreach($halls as $hall)
        {
            $new_hall     = new \App\Models\Hall();
            foreach($hall as $k => $value)
            {
                $new_hall->{$k} = $value;
            }

            $new_hall->save();
        }
    }
}
