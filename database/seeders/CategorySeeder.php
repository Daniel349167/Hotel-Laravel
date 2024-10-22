<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories =
        [
            [
                'descripcion'         => 'INDIVIDUAL',
                'precio'              => 30
            ],
            [
                'descripcion'         => 'DOBLE',
                'precio'              => 50
            ],
            [
                'descripcion'         => 'MATRIMONIAL',
                'precio'              => 60
            ],
            [
                'descripcion'         => 'FAMILIAR',
                'precio'              => 80
            ]
        ];

        foreach($categories as $category)
        {
            $new_category     = new \App\Models\Category();
            foreach($category as $k => $value)
            {
                $new_category->{$k} = $value;
            }

            $new_category->save();
        }
    }
}
