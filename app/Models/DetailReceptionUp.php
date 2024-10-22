<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReceptionUp extends Model
{
    use HasFactory;
    protected $table        = 'detail_reception_ups';
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
        'precio_total',
        'pagado',
    ];
}
