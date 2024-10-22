<?php

namespace App\Http\Controllers;

use App\Models\ArchingCash;
use App\Models\Billing;
use App\Models\Business;
use App\Models\Client;
use App\Models\Currency;
use App\Models\DetailBilling;
use App\Models\DetailPayment;
use App\Models\DetailReception;
use App\Models\DetailReceptionUp;
use App\Models\Hall;
use App\Models\IdentityDocumentType;
use App\Models\IgvTypeAffection;
use App\Models\PayMode;
use App\Models\Product;
use App\Models\Reception;
use App\Models\Room;
use App\Models\Serie;
use App\Models\TypeDocument;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Luecano\NumeroALetras\NumeroALetras;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class RecepcionController extends Controller
{

    public function index()
    {
        return view('admin.receptions.list');
    }

    public function get()
    {
        $receptions     = Reception::select('receptions.*', 'clients.dni_ruc as dni_ruc', 
                    'clients.nombres as cliente', 
                    'type_documents.descripcion as tipo_documento', 'rooms.descripcion as habitacion')
                    ->join('rooms', 'receptions.idhabitacion', 'rooms.id')
                    ->join('clients', 'receptions.idcliente', '=', 'clients.id')
                    ->join('type_documents', 'receptions.idtipo_comprobante', 'type_documents.id')
                    ->orderBy('id', 'DESC')
                    ->get();

        return Datatables()
                    ->of($receptions)
                    ->addColumn('cliente', function ($receptions) {
                        $cliente  = $receptions->cliente;
                        return $cliente;
                    })
                    ->addColumn('fecha_de_entrada', function ($receptions) {
                        $fecha_entrada = date('d-m-Y', strtotime($receptions->fecha_entrada));
                        return $fecha_entrada;
                    })
                    ->addColumn('fecha_de_salida', function ($receptions) {
                        $fecha_salida = date('d-m-Y', strtotime($receptions->fecha_salida));
                        return $fecha_salida;
                    })
                    ->addColumn('estado_recepcion', function ($receptions) 
                    {
                        $estado    = $receptions->estado;
                        $btn    = '';
                        switch ($estado) {
                            case '0':
                                $btn .= '<span class="badge text-white" style="background-color: rgb(108, 117, 125);">Hospedado</span>';
                                break;

                            case '1':
                                $btn .= '<span class="badge bg-success text-white">Culminado</span>';
                                break;
                        }
                        return $btn;
                    })
                    ->addColumn('acciones', function($receptions){
                        $id     = $receptions->id;
                        $idtipo_comprobante = $receptions->idtipo_comprobante;
                        $btn    = '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18"/></svg></button>
                            <div class="dropdown-menu">
                                        <a class="dropdown-item btn-confirm" data-idtipo_comprobante="'.$idtipo_comprobante.'" data-id="'.$id.'" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file mr-50 menu-icon"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                            <span>Generar Boleta/Factura</span>
                            </a>
                        </div>
                    </div>';
                        return $btn;
                    })
                    ->rawColumns(['fecha_de_entrada', 'estado_recepcion', 'fecha_de_salida' ,'acciones'])
                    ->make(true); 
    }

    public function load_rooms(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $halls              = Hall::get();
        $rooms              = Room::select('rooms.*', 'categories.descripcion as categoria', 'categories.precio as precio')
                            ->join('categories', 'rooms.idcategoria', 'categories.id')
                            ->get();

        $html_tables        = '';
        foreach ($halls as $hall) {
            $active = ($hall["id"] == 1) ? 'active show' : '';
            $html_tables .= '<div class="tab-pane fade ' . $active . '" id="navs-pills-hall-' . $hall["id"] . '" role="tabpanel" style="height: calc(100% - 40px);">
                          <div class="row">';
            foreach ($rooms as $table) {
                $class_status      = '';
                $text_status       = '';
                $root_img          = '';

                switch($table["estado"]) {
                    case 1:
                        $class_status .= 'success';
                        $text_status  .= 'DISPONIBLE';
                        $root_img     .= 'assets/img/elements/available.png';
                        break;

                    case 2:
                        $class_status .= 'warning';
                        $text_status  .= 'OCUPADO';
                        $root_img     .= 'assets/img/elements/ocuped.png';
                        break;

                    case 3:
                        $class_status .= 'info';
                        $text_status  .= 'LIMPIEZA';
                        $root_img     .= 'assets/img/elements/clean.png';
                        break;
                }
                if ($table["idsala"] == $hall["id"]) {
                    $html_tables .= '<div class="col-xl-2 col-lg-4 col-md-3 pb-3">
                                                    <div class="card px-0 pt-3 h-100 shadow-none border">
                                                        <div class="rounded-2 text-center mb-2">
                                                        <a href="" class="btn-valid-room" data-status="'.$table["estado"].'" data-id="'.$table["id"].'"><img style="max-width: 60%;
                                                        height: auto;" src="' . asset("$root_img") . '" alt="Image utensils"></a>
                                                        </div>
                                                        <div class="card-body py-0 pt-2">
                                                        <div class="d-flex justify-content-center align-items-center mb-0">
                                                            <span class="badge bg-label-' . $class_status . '">' . $text_status . '</span>
                                                        </div>
                                                        <div class="d-flex justify-content-center align-items-center" style="margin-bottom: -12px;">
                                                            <a class="h5 mt-3 btn-valid-room" data-status="'.$table["estado"].'" data-id="'.$table["id"].'" href="">' . $table["descripcion"] . '</a>
                                                            </div>
                                                            <small class="d-block mb-2 text-center">'. $table["categoria"] .'</small>
                                                        </div>
                                                    </div>
                                                </div>';
                }
            }
            $html_tables .= '</div></div>';
        }

        echo json_encode([
            'status'    => true,
            'html'      => $html_tables
        ]);
    }

    public function enable(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }
        $id         = $request->input('id');
        Room::where('id', $id)->update([
            'estado'    => 1
        ]);

        echo json_encode([
            'status'    => true,
            'msg'       => 'Habitación disponible',
            'type'      => 'success'
        ]);
    }

    public function register($id)
    {
        $room = Room::select('rooms.*', 'categories.descripcion as categoria', 'categories.precio as precio', 'halls.descripcion as sala')
                            ->join('categories', 'rooms.idcategoria', 'categories.id')
                            ->join('halls', 'rooms.idsala', 'halls.id')
                            ->where('rooms.id', $id)
                            ->first();
        $data["idroom"]             = $id;
        $data["room"]               = $room;
        $data["clients"]            = Client::where('id', '!=', 1)->get();
        $data['type_documents']     = IdentityDocumentType::where('estado', 1)->get();
        if ($room->estado != 1 && $room->idrecepcion != NULL) {
            $data["reception"]      = Reception::where('id', $room->idrecepcion)->first();
            $data["idrecepcion"]    = $data["reception"]->id;
            $data["detail"]         = DetailReceptionUp::select('detail_reception_ups.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_reception_ups.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_reception_ups.idrecepcion', $data["reception"]->id)
                                        ->get();
            $data["client"]         = Client::where('id', $data["reception"]->idcliente)->first();
            $data["products"]       = Product::where('id', '!=', 1)->get();
            $data["units"]          = Unit::where('estado', 1)->get();
            $data['type_inafects']  = IgvTypeAffection::where('estado', 1)->get();
            return view('admin.receptions.update.home', $data);
        }

        return view('admin.receptions.register.home', $data);
    }

    public function get_products_update()
    {
        $products           = Product::where('id', '!=', 1)->orderBy('id', 'DESC')->get();
        return $products;
    }

    public function create()
    {
        $data['halls']  = Hall::get();
        return view('admin.receptions.create.home', $data);
    }

    public function save(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $idroom             = $request->input('idroom');
        $room               = Room::select('rooms.*', 'categories.descripcion as categoria', 'categories.precio as precio')
                            ->join('categories', 'rooms.idcategoria', 'categories.id')
                            ->first();
        $fecha_entrada      = date('Y-m-d');
        $precio             = $request->input('precio');
        $idcliente          = $request->input('dni_ruc');
        if(empty($idcliente)) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Debe seleccionar un cliente',
                'type'      => 'warning'
            ]);
            return;
        }

        $cliente            = Client::where('id', $idcliente)->first();
        $idtipo_comprobante = (strlen($cliente->dni_ruc) == 11) ? 1 : 2;
        $fecha_salida       = $request->input('fecha_salida');
        $adelanto           = $request->input('adelanto');
        $diferencia         = $request->input('diferencia');
        $observacion        = trim($request->input('observacion'));

        

        $config_tax          = $this->config_tax();
        $impuesto            = $config_tax["impuesto"];
        $codigo_igv          = $config_tax["codigo_igv"];
        $exonerada           = 0;
        $gravada             = 0;
        $inafecta            = 0;
        $subtotal            = 0;
        $total               = 0;
        $igv                 = 0;

        if($impuesto == 1) {
            $igv        +=  number_format((((float) $precio - (float) $precio/ 1.18) ), 2, ".", "");
            $igv        = $this->redondeado($igv);
        }

        if($codigo_igv == 10) {
            $gravada    += number_format((((float) $precio / 1.18)), 2, ".", "");
            $gravada     = $this->redondeado($gravada);
        }

        if($codigo_igv == 20) {
            $exonerada   += number_format(((float) $precio), 2, ".", "");
            $exonerada   = $this->redondeado($exonerada);
        }

        if($codigo_igv == 30) {
            $inafecta    += number_format(((float) $precio), 2, ".", "");
            $inafecta     = str_replace(',', '', $inafecta);
            $inafecta     = $this->redondeado($inafecta);
        }

        $subtotal         = $exonerada + $gravada + $inafecta;
        $total            = $subtotal + $igv;
        $id_arching       = ArchingCash::where('idcaja', Auth::user()['idcaja'])->where('idusuario', Auth::user()['id'])->latest('id')->first()['id'];

        Reception::insert([
            'idtipo_comprobante'        => $idtipo_comprobante,
            'fecha_emision'             => date('Y-m-d'),
            'fecha_vencimiento'         => date('Y-m-d'),
            'fecha_entrada'             => $fecha_entrada,
            'fecha_salida'              => $fecha_salida,
            'hora'                      => date('H:i:s'),
            'idcliente'                 => $idcliente,
            'idmoneda'                  => 1,
            'idpago'                    => 1,
            'modo_pago'                 => 1,
            'exonerada'                 => $exonerada,
            'inafecta'                  => $inafecta,
            'gravada'                   => $gravada,
            'anticipo'                  => "0.00",
            'igv'                       => $igv,
            'gratuita'                  => "0.00",
            'otros_cargos'              => "0.00",
            'total'                     => $total,
            'observaciones'             => mb_strtoupper($observacion),
            'estado'                    => 0,
            'idhabitacion'              => $idroom,
            'idusuario'                 => Auth::user()['id'],
            'idcaja'                    => $id_arching,
            'adelanto'                  => $adelanto,
            'diferencia'                => $diferencia
        ]); 
        
        $idrecepcion      = Reception::latest('id')->first()['id'];

        DetailReception::insert([
            'idrecepcion'           => $idrecepcion,
            'idproducto'            => 1,
            'cantidad'              => 1,
            'descuento'             => 0.0000000000,
            'igv'                   => $igv,
            'id_afectacion_igv'     => $codigo_igv,
            'precio_unitario'       => $precio,
            'precio_total'          => $precio
        ]);

        // Cambiar de estado a la habitación
        Room::where('id', $idroom)->update([
            'estado'        => 2,
            'idrecepcion'   => $idrecepcion
        ]);
        
        echo json_encode([
            'status'    => true,
            'msg'       => 'Recepción realizada con éxito',
            'type'      => 'success',
        ]);
    }

    public function get_reception_update(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $idrecepcion                = $request->input('idrecepcion');
        $recepcion                  = Reception::where('id', $idrecepcion)->first();
        $observaciones              = trim($request->input('observaciones'));
        $products                   = json_decode($request->post('productos'));
        $totales                    = json_decode($request->post('totales'));
        $precio_total               = $request->input('precio_total');
        $fecha_salida               = $request->input('fecha_salida');

        $registros                  = DetailReceptionUp::where('idrecepcion', $idrecepcion)->get();
        $existingIdentifiers        = $registros->pluck('idproducto')->toArray();
        $array_ids                  = [];
        $array_precio               = [];
        $array_cantidad             = [];

        foreach($products as $producto) 
        {
            $array_ids[]            = $producto->idproducto;
            $array_precio[]         = $producto->precio;
            $array_cantidad[]       = $producto->cantidad;
        }

        if(empty($existingIdentifiers)) {
            foreach($products as $producto) {
                $search_product         = Product::where('id', $producto->idproducto)->first();
                DetailReceptionUp::updateOrCreate([
                    'idrecepcion'       => $idrecepcion,
                    'idproducto'        => $producto->idproducto
                ], [
                    'idrecepcion'       => $idrecepcion,
                    'idproducto'        => $producto->idproducto,
                    'cantidad'          => $producto->cantidad,
                    'precio_unitario'   => $producto->precio,
                    'descuento'         => 0.0000000000,
                    'igv'               => $search_product->igv,
                    'id_afectacion_igv' => $search_product->idcodigo_igv,
                    'precio_total'      => ($producto->precio * $producto->cantidad),
                    'pagado'            => ($producto->pagado) ? 1 : 0,
                ]);
            }
        }
        else {
            foreach($existingIdentifiers as $i => $id_db)
            {
                if(in_array($id_db, $array_ids)) {
                    foreach($products as $producto) {
                        $search_product         = Product::where('id', $producto->idproducto)->first();
                        DetailReceptionUp::updateOrCreate([
                            'idrecepcion'       => $idrecepcion,
                            'idproducto'        => $producto->idproducto
                        ], [
                            'idrecepcion'       => $idrecepcion,
                            'idproducto'        => $producto->idproducto,
                            'cantidad'          => $producto->cantidad,
                            'precio_unitario'   => $producto->precio,
                            'descuento'         => 0.0000000000,
                            'igv'               => $search_product->igv,
                            'id_afectacion_igv' => $search_product->idcodigo_igv,
                            'precio_total'      => ($producto->precio * $producto->cantidad),
                            'pagado'            => ($producto->pagado) ? 1 : 0 
                        ]);
                    }
                } 
                else {
                    DetailReceptionUp::where([
                        'idrecepcion'       => $idrecepcion,
                        'idproducto'        => $id_db
                    ])->delete();
                }
            }
        }

        DetailReception::where('idrecepcion', $idrecepcion)->update([
            'precio_total'      => $precio_total,
            'precio_unitario'   => $precio_total
        ]);

        // Calculate IGV, etc
        $exonerada_first        = 0;
        $gravada_first          = 0;
        $inafecta_first         = 0;
        $igv_first              = 0;

        $exonerada_last         = 0;
        $gravada_last           = 0;
        $inafecta_last          = 0;
        $igv_last               = 0;
        $subtotal_first         = 0;
        $subtotal_last          = 0;

        $detalle_first          = DetailReception::select('detail_receptions.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_receptions.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_receptions.idrecepcion', $idrecepcion)
                                        ->get();

        $detalle_last         = DetailReceptionUp::select('detail_reception_ups.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_reception_ups.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_reception_ups.idrecepcion', $idrecepcion)
                                        ->get();

        foreach($detalle_first as $product) {
            if ($product['impuesto'] == 1) {
                $igv_first        +=  number_format((((float) $product['precio_unitario'] - (float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                $igv_first        = $this->redondeado($igv_first);
            }

            if ($product["codigo_igv"] == "10") {
                $gravada_first    += number_format((((float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                $gravada_first     = $this->redondeado($gravada_first);
            }

            if ($product["codigo_igv"] == "20") {
                $exonerada_first   += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                $exonerada_first   = $this->redondeado($exonerada_first);
            }

            if ($product["codigo_igv"] == "30") {
                $inafecta_first    += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                $inafecta_first     = str_replace(',', '', $inafecta_first);
                $inafecta_first     = $this->redondeado($inafecta_first);
            }
            $subtotal_first   = $exonerada_first + $gravada_first + $inafecta_first;
        }

        if(!empty($detalle_last)) {
            foreach($detalle_last as $product) {
                if ($product['impuesto'] == 1) {
                    $igv_last        +=  number_format((((float) $product['precio_unitario'] - (float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                    $igv_last        = $this->redondeado($igv_last);
                }
    
                if ($product["codigo_igv"] == "10") {
                    $gravada_last    += number_format((((float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                    $gravada_last     = $this->redondeado($gravada_last);
                }
    
                if ($product["codigo_igv"] == "20") {
                    $exonerada_last   += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                    $exonerada_last   = $this->redondeado($exonerada_last);
                }
    
                if ($product["codigo_igv"] == "30") {
                    $inafecta_last    += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                    $inafecta_last     = str_replace(',', '', $inafecta_last);
                    $inafecta_last     = $this->redondeado($inafecta_last);
                }
                $subtotal_last   = $exonerada_last + $gravada_last + $inafecta_last;
            }
        }

        Reception::where('id', $idrecepcion)->update([
            'fecha_salida'  => $fecha_salida,
            'exonerada'     => $exonerada_first + $exonerada_last,
            'inafecta'      => $inafecta_first + $inafecta_last,
            'gravada'       => $gravada_first + $gravada_last,
            'anticipo'      => "0.00",
            'igv'           => $igv_first + $igv_last,
            'gratuita'      => "0.00",
            'otros_cargos'  => "0.00",
            'total'         => $subtotal_first + $subtotal_last,
            'observaciones' => mb_strtoupper($observaciones),
        ]);

        echo json_encode([
            'status'    => true,
            'msg'       => 'Datos guardados correctamente'
        ]);
    }

    public function exit()
    {
        $data['halls']  = Hall::get();
        return view('admin.receptions.exit.home', $data);
    }

    public function load_rooms_exit(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $halls              = Hall::get();
        $rooms              = Room::select('rooms.*', 'categories.descripcion as categoria', 'categories.precio as precio')
                            ->join('categories', 'rooms.idcategoria', 'categories.id')
                            ->where('rooms.estado', 2)
                            ->get();

        $html_tables        = '';
        foreach ($halls as $hall) {
            $active = ($hall["id"] == 1) ? 'active show' : '';
            $html_tables .= '<div class="tab-pane fade ' . $active . '" id="navs-pills-hall-' . $hall["id"] . '" role="tabpanel" style="height: calc(100% - 40px);">
                          <div class="row">';
            foreach ($rooms as $table) {
                $class_status      = '';
                $text_status       = '';
                $root_img          = '';

                switch($table["estado"]) {
                    case 1:
                        $class_status .= 'success';
                        $text_status  .= 'DISPONIBLE';
                        $root_img     .= 'assets/img/elements/available.png';
                        break;

                    case 2:
                        $class_status .= 'warning';
                        $text_status  .= 'OCUPADO';
                        $root_img     .= 'assets/img/elements/ocuped.png';
                        break;

                    case 3:
                        $class_status .= 'info';
                        $text_status  .= 'LIMPIEZA';
                        $root_img     .= 'assets/img/elements/clean.png';
                        break;
                }
                if ($table["idsala"] == $hall["id"]) {
                    $html_tables .= '<div class="col-xl-2 col-lg-4 col-md-3 pb-3">
                                                    <div class="card px-0 pt-3 h-100 shadow-none border">
                                                        <div class="rounded-2 text-center mb-2">
                                                        <a href="'. route('admin.register_exit', $table["id"]) .'" data-status="'.$table["estado"].'" data-id="'.$table["id"].'"><img style="max-width: 60%;
                                                        height: auto;" src="' . asset("$root_img") . '" alt="Image utensils"></a>
                                                        </div>
                                                        <div class="card-body py-0 pt-2">
                                                        <div class="d-flex justify-content-center align-items-center mb-0">
                                                            <span class="badge bg-label-' . $class_status . '">' . $text_status . '</span>
                                                        </div>
                                                        <div class="d-flex justify-content-center align-items-center" style="margin-bottom: -12px;">
                                                            <a href="'. route('admin.register_exit', $table["id"]) .'" class="h5 mt-3" data-status="'.$table["estado"].'" data-id="'.$table["id"].'" href="">' . $table["descripcion"] . '</a>
                                                            </div>
                                                            <small class="d-block mb-2 text-center">'. $table["categoria"] .'</small>
                                                        </div>
                                                    </div>
                                                </div>';
                }
            }
            $html_tables .= '</div></div>';
        }

        echo json_encode([
            'status'    => true,
            'html'      => $html_tables
        ]);
    }

    public function register_exit($id)
    {
        $room                       = Room::select('rooms.*', 'categories.descripcion as categoria', 'categories.precio as precio', 
                                    'halls.descripcion as sala')
                                    ->join('categories', 'rooms.idcategoria', 'categories.id')
                                    ->join('halls', 'rooms.idsala', 'halls.id')
                                    ->where('rooms.id', $id)
                                    ->first();
        $data["idroom"]             = $id;
        $data["room"]               = $room;
        $data["reception"]          = Reception::where('id', $room->idrecepcion)->first();
        $data["idrecepcion"]        = $data["reception"]->id;
        $data["detail"]             = DetailReceptionUp::select('detail_reception_ups.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_reception_ups.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_reception_ups.idrecepcion', $data["reception"]->id)
                                        ->get();
        $data["sum_detail"]     = DetailReceptionUp::where('detail_reception_ups.idrecepcion', $data["reception"]->id)->sum('precio_total');
        $data["client"]         = Client::where('id', $data["reception"]->idcliente)->first();
        return view('admin.receptions.register_exit.home', $data);
    }

    public function gen_reception_exit(Request $request)
    {
        if (!$request->ajax()) {
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $idrecepcion                = $request->input('idrecepcion');
        $recepcion                  = Reception::where('id', $idrecepcion)->first();
        $suma_detalle               = DetailReception::where('idrecepcion', $idrecepcion)->sum('precio_total');
        $observaciones              = trim($request->input('observaciones'));
        $products                   = json_decode($request->post('productos'));
        $totales                    = json_decode($request->post('totales'));
        $precio_total               = $request->input('precio_total');
        $mora                       = (float) $request->input('mora');
        $diferencia                 = $request->input('diferencia');
        $idroom                     = $request->input('idroom');

        if($mora > 0) {
            DetailReception::where('idrecepcion', $idrecepcion)->update([
                'precio_unitario'   => $suma_detalle + $mora,
                'precio_total'      => $suma_detalle + $mora,
            ]);
        }

        $detalle_first              = DetailReception::select('detail_receptions.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_receptions.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_receptions.idrecepcion', $idrecepcion)
                                        ->get();

        $detalle_last               = DetailReceptionUp::select('detail_reception_ups.*', 'products.descripcion as producto',
                                        'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv', 'igv_type_affections.codigo as codigo_igv',
                                        'products.igv as igv', 'products.opcion as opcion', 'products.impuesto as impuesto')
                                        ->join('products', 'detail_reception_ups.idproducto', '=', 'products.id')
                                        ->join('units', 'products.idunidad', '=', 'units.id')
                                        ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                        ->where('detail_reception_ups.idrecepcion', $idrecepcion)
                                        ->get();

        $exonerada_first              = 0;
        $gravada_first                = 0;
        $inafecta_first               = 0;
        $igv_first                    = 0;
        $subtotal_first               = 0;

        $exonerada_last               = 0;
        $gravada_last                 = 0;
        $inafecta_last                = 0;
        $igv_last                     = 0;
        $subtotal_last                = 0;
    

        foreach($detalle_first as $product) {
            if ($product['impuesto'] == 1) {
                $igv_first        +=  number_format((((float) $product['precio_unitario'] - (float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                $igv_first        = $this->redondeado($igv_first);
            }

            if ($product["codigo_igv"] == "10") {
                $gravada_first    += number_format((((float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                $gravada_first     = $this->redondeado($gravada_first);
            }

            if ($product["codigo_igv"] == "20") {
                $exonerada_first   += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                $exonerada_first   = $this->redondeado($exonerada_first);
            }

            if ($product["codigo_igv"] == "30") {
                $inafecta_first    += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                $inafecta_first     = str_replace(',', '', $inafecta_first);
                $inafecta_first     = $this->redondeado($inafecta_first);
            }
            $subtotal_first            = $exonerada_first + $gravada_first + $inafecta_first;
        }

        if(!empty($detalle_last)) {
            foreach($detalle_last as $product) {
                if ($product['impuesto'] == 1) {
                    $igv_last        +=  number_format((((float) $product['precio_unitario'] - (float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                    $igv_last        = $this->redondeado($igv_last);
                }
    
                if ($product["codigo_igv"] == "10") {
                    $gravada_last    += number_format((((float) $product['precio_unitario'] / 1.18) * (int) $product['cantidad']), 2, ".", "");
                    $gravada_last     = $this->redondeado($gravada_last);
                }
    
                if ($product["codigo_igv"] == "20") {
                    $exonerada_last   += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                    $exonerada_last   = $this->redondeado($exonerada_last);
                }
    
                if ($product["codigo_igv"] == "30") {
                    $inafecta_last    += number_format(((float) $product['precio_unitario'] * (int) $product['cantidad']), 2, ".", "");
                    $inafecta_last     = str_replace(',', '', $inafecta_last);
                    $inafecta_last     = $this->redondeado($inafecta_last);
                }
                $subtotal_last   = $exonerada_last + $gravada_last + $inafecta_last;
            }
        }

        DetailReceptionUp::where('idrecepcion', $idrecepcion)->update([
            'pagado'    => 1 
        ]);
        
        Reception::where('id', $idrecepcion)->update([
            'exonerada'     => $exonerada_first + $exonerada_last,
            'inafecta'      => $inafecta_first + $inafecta_last,
            'gravada'       => $gravada_first + $gravada_last,
            'anticipo'      => "0.00",
            'igv'           => $igv_first + $igv_last,
            'gratuita'      => "0.00",
            'otros_cargos'  => "0.00",
            'total'         => $subtotal_first + $subtotal_last,
            'observaciones' => mb_strtoupper($observaciones),
            'estado'        => 1
        ]);

        Room::where('id', $idroom)->update([
            'estado'        => 3,
            'idrecepcion'   => NULL
        ]);

        echo json_encode([
            'status'        => true,
            'msg'           => 'Salida registrada correctamente'
        ]);
    }

    public function gen_voucher(Request $request)
    {
        if(!$request->ajax()){
            echo json_encode([
                'status'    => false,
                'msg'       => 'Intente de nuevo',
                'type'      => 'warning'
            ]);
            return;
        }

        $id                     = $request->input('id');
        $recepcion              = Reception::where('id', $id)->first();
        $detalle_first          = DetailReception::select('detail_receptions.*', 'products.descripcion as producto',
                                'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv',
                                'products.igv as igv', 'products.opcion as opcion')
                                ->join('products', 'detail_receptions.idproducto', '=', 'products.id')
                                ->join('units', 'products.idunidad', '=', 'units.id')
                                ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                ->where('detail_receptions.idrecepcion', $id)
                                ->get();

        $detalle_last          = DetailReceptionUp::select('detail_reception_ups.*', 'products.descripcion as producto',
                                'products.codigo_interno as codigo_interno','units.codigo as unidad', 'products.idcodigo_igv as idcodigo_igv',
                                'products.igv as igv', 'products.opcion as opcion')
                                ->join('products', 'detail_reception_ups.idproducto', '=', 'products.id')
                                ->join('units', 'products.idunidad', '=', 'units.id')
                                ->join('igv_type_affections', 'products.idcodigo_igv', 'igv_type_affections.id')
                                ->where('detail_reception_ups.idrecepcion', $id)
                                ->get();

        $client                 = Client::where('id', $recepcion->idcliente)->first();
        $idtipo_comprobante     = $request->input('idtipo_comprobante');
        $fecha_emision          = date('Y-m-d');
        $fecha_vencimiento      = date('Y-m-d');
        $id_arching             = ArchingCash::where('idcaja', Auth::user()['idcaja'])->where('idusuario', Auth::user()['id'])->latest('id')->first()['id'];

        // Save
        $business                   = Business::where('id', 1)->first();
        $type_document              = TypeDocument::where('id', $idtipo_comprobante)->first();
        $client                     = Client::where('id', $client->id)->first();
        $identity_document          = IdentityDocumentType::where('id', $client->iddoc)->first();

        $ultima_serie               = Serie::where('idtipo_documento', $idtipo_comprobante)->where('idcaja', Auth::user()['idcaja'])->first();
        $ultimo_correlativo         = (int) $ultima_serie->correlativo;
        $serie                      = $ultima_serie->serie;
        $correlativo                = str_pad($ultimo_correlativo, 8, '0', STR_PAD_LEFT);
        
        $qr                         = $business->ruc . ' | ' . $type_document->codigo . ' | ' . $serie . ' | ' . $correlativo . ' | ' . number_format($recepcion->igv, 2, ".", "") . ' | ' . number_format($recepcion->total, 2, ".", "") . ' | ' . $fecha_emision . ' | ' . $identity_document->codigo . ' | ' . $client->dni_ruc;
        $name_qr                    = $serie . '-' . $correlativo;

        // Gen Qr
        QrCode::format('png')
        ->size(140)
        ->generate($qr, 'files/billings/qr/' . $name_qr . '.png');

        Billing::insert([
            'idtipo_comprobante'    => $idtipo_comprobante,
            'serie'                 => $serie,
            'correlativo'           => $correlativo,
            'fecha_emision'         => $fecha_emision,
            'fecha_vencimiento'     => $fecha_vencimiento,
            'hora'                  => date('H:i:s'),
            'idcliente'             => $client->id,
            'idmoneda'              => 1,
            'idpago'                => 1,
            'modo_pago'             => $recepcion->modo_pago,
            'exonerada'             => $recepcion->exonerada,
            'inafecta'              => $recepcion->inafecta,
            'gravada'               => $recepcion->gravada,
            'anticipo'              => "0.00",
            'igv'                   => $recepcion->igv,
            'gratuita'              => "0.00",
            'otros_cargos'          => "0.00",
            'total'                 => $recepcion->total,
            'cdr'                   => 0,
            'anulado'               => 0,
            'id_tipo_nota_credito'  => null,
            'estado_cpe'            => 0,
            'errores'               => null,
            'nticket'               => null,
            'idusuario'             => Auth::user()['id'],
            'idcaja'                => $id_arching,
            'vuelto'                => "0.00",
            'qr'                    => $name_qr . '.png'
        ]);
        $idfactura                  = Billing::latest('id')->first()['id'];
        DetailPayment::insert([
            'idtipo_comprobante'    => $idtipo_comprobante,
            'idfactura'             => $idfactura,
            'idpago'                => $recepcion->modo_pago,
            'monto'                 => $recepcion->total,
            'idcaja'                => $id_arching
        ]);

        foreach ($detalle_first as $product) {
            DetailBilling::insert([
                'idfacturacion'         => $idfactura,
                'idproducto'            => $product['idproducto'],
                'cantidad'              => $product['cantidad'],
                'descuento'             => 0.0000000000,
                'igv'                   => $product["igv"],
                'id_afectacion_igv'     => $product['idcodigo_igv'],
                'precio_unitario'       => $product['precio_unitario'],
                'precio_total'          => ($product['precio_unitario'] * $product['cantidad'])
            ]);
        }

        if(!empty($detalle_last)) {
            foreach ($detalle_last as $product) {
                DetailBilling::insert([
                    'idfacturacion'         => $idfactura,
                    'idproducto'            => $product['idproducto'],
                    'cantidad'              => $product['cantidad'],
                    'descuento'             => 0.0000000000,
                    'igv'                   => $product["igv"],
                    'id_afectacion_igv'     => $product['idcodigo_igv'],
                    'precio_unitario'       => $product['precio_unitario'],
                    'precio_total'          => ($product['precio_unitario'] * $product['cantidad'])
                ]);
            }
        }

        $factura                = Billing::where('id', $idfactura)->first();
        $ruc                    = Business::where('id', 1)->first()->ruc;
        $code_sale              = TypeDocument::where('id', $factura->idtipo_comprobante)->first()->codigo;
        $name_sale              = $ruc . '-' . $code_sale . '-' . $factura->serie . '-' . $factura->correlativo;
        $id_sale                = $idfactura;
        $this->gen_ticket_b($idfactura, $name_sale);

        $ultima_serie_sale      = Serie::where('idtipo_documento', $idtipo_comprobante)->where('idcaja', Auth::user()['idcaja'])->first();
        $ultimo_correlativo_sale= (int) $ultima_serie_sale->correlativo + 1;
        $nuevo_correlativo_sale = str_pad($ultimo_correlativo_sale, 8, '0', STR_PAD_LEFT);
        Serie::where('idtipo_documento', $idtipo_comprobante)->where('idcaja', Auth::user()['idcaja'])->update([
            'correlativo'   => $nuevo_correlativo_sale
        ]);

        echo json_encode([
            'status'        => true,
            'id'            => $id_sale,
            'pdf'           => $name_sale . '.pdf',
            'type_document' => $idtipo_comprobante
        ]);
    }

    public function gen_ticket_b($id, $name)
    {
        $customPaper                = array(0, 0, 630.00, 210.00);
        $data['business']           = Business::where('id', 1)->first();
        $data['ubigeo']             = $this->get_ubigeo($data['business']->ubigeo);
        $ruc                        = $data['business']->ruc;
        $factura                    = Billing::where('id', $id)->first();
        $codigo_comprobante         = TypeDocument::where('id', $factura->idtipo_comprobante)->first()->codigo;
        $data["name"]               = $ruc . '-' . $codigo_comprobante . '-' . $factura->serie . '-' . $factura->correlativo;

        $data['factura']            = Billing::where('id', $id)->first();
        $data['cliente']            = Client::where('id', $factura->idcliente)->first();
        $data['tipo_documento']     = IdentityDocumentType::where('id', $data['cliente']->iddoc)->first();
        $data['moneda']             = Currency::where('id', $factura->idmoneda)->first();
        $data['modo_pago']          = PayMode::where('id', $factura->modo_pago)->first();
        $data['detalle']            = DetailBilling::select(
            'detail_billings.*',
            'products.descripcion as producto',
            'products.codigo_interno as codigo_interno'
        )
            ->join('products', 'detail_billings.idproducto', '=', 'products.id')
            ->where('idfacturacion', $factura->id)
            ->get();

        $formatter                  = new NumeroALetras();
        $data['numero_letras']      = $formatter->toWords($factura->total, 2);
        $data['tipo_comprobante']   = TypeDocument::where('id', $factura->idtipo_comprobante)->first();
        $data['vendedor']           = mb_strtoupper(User::where('id', $data['factura']->idusuario)->first()->user);
        $data['payment_modes']      = DetailPayment::select('detail_payments.*', 'pay_modes.descripcion as modo_pago')
            ->join('pay_modes', 'detail_payments.idpago', 'pay_modes.id')
            ->where('idfactura', $factura->id)
            ->where('idtipo_comprobante', $factura->idtipo_comprobante)
            ->get();
        $data['count_payment']      = count($data['payment_modes']);
        $pdf                        = PDF::loadView('admin.billings.ticket_b', $data)->setPaper($customPaper, 'landscape');
        return $pdf->save(public_path('files/billings/ticket/' . $name . '.pdf'));
    }
}
