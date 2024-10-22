<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;
    protected $table = 'receptions';
    protected $primaryKey = 'id';
    protected $fillable = 
    [
        'idtipo_comprobante',
        'fecha_emision',
        'fecha_vencimiento',
        'fecha_entrada',
        'fecha_salida',
        'hora',
        'idcliente',
        'idmoneda',
        'idpago',
        'modo_pago',
        'exonerada',
        'inafecta',
        'gravada',
        'anticipo',
        'igv',
        'gratuita',
        'otros_cargos',
        'total',
        'observaciones',
        'estado',
        'idhabitacion',
        'idusuario',
        'idcaja',
        'idfactura_anular',
        'adelanto',
        'diferencia'
    ];
}
