<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products =
        [
            [
                'codigo_sunat'      => '00000000',
                'codigo_interno'    => NULL,
                'codigo_barras'     => NULL,
                'descripcion'       => "RESERVACION",
                'marca'             => NULL,
                'presentacion'      => NULL,
                'idunidad'          => 61,
                'idcodigo_igv'      => 10, // 1 o 10,
                'igv'               => 0, // 0 o 18,
                'precio_compra'     => 0.00,
                'precio_venta'      => 0.00,
                'impuesto'          => 0, // 0 o 1
                'stock'             => NULL,
                'fecha_vencimiento' => NULL,
                'opcion'            => 2
            ],
            [
                'codigo_sunat'      => '00000000',
                'codigo_interno'    => NULL,
                'codigo_barras'     => NULL,
                'descripcion'       => "INKA KOLA",
                'marca'             => NULL,
                'presentacion'      => NULL,
                'idunidad'          => 61,
                'idcodigo_igv'      => 10, // 1 o 10,
                'igv'               => 0, // 0 o 18,
                'precio_compra'     => 0.00,
                'precio_venta'      => 4.00,
                'impuesto'          => 0, // 0 o 1
                'stock'             => NULL,
                'fecha_vencimiento' => NULL,
                'opcion'            => 1
            ],
            [
                'codigo_sunat'      => '00000000',
                'codigo_interno'    => NULL,
                'codigo_barras'     => NULL,
                'descripcion'       => "COCA COLA",
                'marca'             => NULL,
                'presentacion'      => NULL,
                'idunidad'          => 61,
                'idcodigo_igv'      => 10, // 1 o 10,
                'igv'               => 0, // 0 o 18,
                'precio_compra'     => 0.00,
                'precio_venta'      => 4.00,
                'impuesto'          => 0, // 0 o 1
                'stock'             => NULL,
                'fecha_vencimiento' => NULL,
                'opcion'            => 1
            ],
            [
                'codigo_sunat'      => '00000000',
                'codigo_interno'    => NULL,
                'codigo_barras'     => NULL,
                'descripcion'       => "CHOCO CHIPS",
                'marca'             => NULL,
                'presentacion'      => NULL,
                'idunidad'          => 61,
                'idcodigo_igv'      => 10, // 1 o 10,
                'igv'               => 0, // 0 o 18,
                'precio_compra'     => 0.00,
                'precio_venta'      => 2.00,
                'impuesto'          => 0, // 0 o 1
                'stock'             => NULL,
                'fecha_vencimiento' => NULL,
                'opcion'            => 1
            ]
        ];

        foreach($products as $product)
        {
            $new_product     = new \App\Models\Product();
            foreach($product as $k => $value)
            {
                $new_product->{$k} = $value;
            }

            $new_product->save();
        }
    }
}
