<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReception extends Model
{
    use HasFactory;
    protected $table        = 'detail_receptions';
    protected $primaryKey   = 'id';
    protected $fillable     = 
    [
        'idrecepcion',
        'idproducto',
        'cantidad',
        'descuento',
        'igv',
        'id_afectacion_igv',
        'precio_unitario',
        'precio_total'
    ];
}
