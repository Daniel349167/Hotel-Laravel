<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rooms =
        [
            [
                'descripcion'   => 'CUARTO 101',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 102',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 103',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 104',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 3
            ],
            [
                'descripcion'   => 'CUARTO 105',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 106',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 4
            ],
            [
                'descripcion'   => 'CUARTO 107',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 108',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 109',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 110',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 4
            ],
            [
                'descripcion'   => 'CUARTO 111',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 112',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 1,
                'idcategoria'   => 4
            ],
            [
                'descripcion'   => 'CUARTO 201',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 202',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 203',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 204',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 4
            ],
            [
                'descripcion'   => 'CUARTO 205',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 206',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 2,
                'idcategoria'   => 1
            ],
            [
                'descripcion'   => 'CUARTO 301',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 302',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 303',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 2
            ],
            [
                'descripcion'   => 'CUARTO 304',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 3
            ],
            [
                'descripcion'   => 'CUARTO 305',
                'detalle'       => 'WIFI + BAÑO',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 3
            ],
            [
                'descripcion'   => 'CUARTO 306',
                'detalle'       => 'WIFI + BAÑO + TV + CABLE',
                'estado'        => 1,
                'idsala'        => 3,
                'idcategoria'   => 3
            ],
        ];

        foreach($rooms as $room)
        {
            $new_room     = new \App\Models\Room();
            foreach($room as $k => $value)
            {
                $new_room->{$k} = $value;
            }

            $new_room->save();
        }
    }
}
