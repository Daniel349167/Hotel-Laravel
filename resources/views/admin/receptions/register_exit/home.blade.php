@extends('admin.template')
@section('styles')
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endsection
@section('content')
    <section class="basic-select2">
        <div class="row">
            <!-- Congratulations Card -->
            <form id="form-update-reception" class="form form-vertical">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-title text-primary fw-bold mb-4">Resumen de la Habitaci&oacute;n</h5>
                                    <div class="table-responsive-sm">
                                        <table class="table table-hover table-sm">
                                            <tbody>
                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Nombre</th>
                                                    <td colspan="1" class="td__dispatch">
                                                        {{ $room->descripcion . ' (' . $room->detalle . ')' }}</td>

                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Categor&iacute;a</th>
                                                    <td colspan="1" class="td__dispatch">{{ $room->categoria }}</td>

                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Nivel</th>
                                                    <td colspan="1" class="td__dispatch">{{ $room->sala }}</td>
                                                </tr>

                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Cliente</th>
                                                    <td colspan="1" class="td__dispatch">{{ $client->nombres }}</td>

                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Documento</th>
                                                    <td colspan="1" class="td__dispatch">{{ $client->dni_ruc }}</td>

                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Fecha Entrada</th>
                                                    <td colspan="1" class="td__dispatch">
                                                        {{ date('d-m-Y', strtotime($reception->fecha_entrada)) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Congratulations Card -->

                <div class="col-12 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-title text-primary fw-bold mb-4">Detalle de Hospedaje</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Costo Habitaci&oacute;n</label>
                                            <input type="hidden" name="idroom" value="{{ $room->id }}">
                                            <input type="hidden" name="fecha_entrada" value="{{ $reception->fecha_entrada }}">
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ number_format($reception->total - $sum_detail, 2, ".", "") }}" readonly name="precio">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Adelanto</label>
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ $reception->adelanto }}" readonly name="adelanto">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Mora / Penalidad</label>
                                            <input type="text" id="multicol-first-name" class="form-control mora" value="0.00" name="mora">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Por Pagar</label>
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ $reception->diferencia }}" readonly name="diferencia">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row invoice-add mt-3">
                                <div class="col-md-12">
                                    <h5 class="card-title text-primary fw-bold mb-4">Servicio a la Habitaci&oacute;n</h5>
                                    <div class="table-responsive-sm">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="">Descripción</th>
                                                    <th class="text-center d-none">Und.</th>
                                                    <th class="text-center" width="13%">&nbsp;&nbsp;&nbsp;Cantidad&nbsp;&nbsp;&nbsp;</th>
                                                    <th class="text-center" width="14%">Precio Unitario</th>
                                                    <th class="text-center" width="10%">Total</th>
                                                    <th class="text-center" width="10%">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody id="wrapper-tbody">
                                            @foreach ($detail as $i => $product)
                                                    <tr id="tr__product__{{ $product['idproducto'] }}">
                                                        <td class="d-none"><input type="hidden" name="idproducto" value="{{ $product['idproducto'] }}"></td>
                                                        <td class="text-left">{{ $product['producto'] }}</td>
                                                        <td class="text-center d-none">{{ $product['unidad'] }}</td>
                                                        <td class="text-center">{{ intval($product['cantidad']) }}</td>

                                                        <td class="text-center" data-cantidad="{{ $product['cantidad'] }}" data-codigo_igv="{{ $product['codigo_igv'] }}" data-impuesto="{{ $product['impuesto'] }}" data-id="{{ $product['idproducto'] }}" data-idrecepcion="{{ $idrecepcion }}" data-pagado="{{ $product['pagado'] }}">{{ number_format($product['precio_unitario'], 2, '.', '') }}</td>

                                                        <td class="text-center">{{ number_format(($product["precio_unitario"] * $product["cantidad"]), 2, ".", "") }}</td>

                                                        <td class="text-center">
                                                            @switch($product["pagado"])
                                                                @case(0)
                                                                    <span class="badge bg-warning text-white">Pendiente</span>
                                                                    @break
                                                                @case(1)
                                                                    <span class="badge bg-success text-white">Pagado</span>
                                                                    @break
                                                                @default
                                                            @endswitch
                                                        </td>
                                                    </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="mt-5">
                                    <div class="row p-0">
                                        <div class="col-md-4 mb-md-0">
                                            <div class="d-flex align-items-center">
                                            <label for="salesperson" class="form-label me-4 fw-medium">Información Adicional:</label>
                                            </div>
                                            <input type="hidden" name="idrecepcion" value="{{ $reception->id }}">
                                            <textarea class="form-control text-uppercase" cols="8" rows="3" name="observaciones">{{ $reception->observaciones }}</textarea>
                                        </div>

                                        <div id="wrapper-totals" class="col-md-8 d-flex justify-content-end mt-3">
                                            <div class="invoice-calculations">
                                                <span class="d-none span__exonerada"></span>
                                                <span class="d-none span__gravada"></span>
                                                <span class="d-none span__inafecta"></span>
                                                <div class="d-flex justify-content-between">
                                                    <span class="w-px-100">OP. Gravadas:</span>
                                                    <span class="fw-medium">Q/ <span class="span__subtotal">{{ number_format(($reception->exonerada + $reception->gravada + $reception->inafecta), 2) }}</span> </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="w-px-100">IGV:</span>
                                                    <span class="fw-medium">Q/<span class="span__igv">{{ number_format(($reception->igv), 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="w-px-100">Total a Pagar:</span>
                                                    <span class="fw-medium">Q/<span class="span__total">{{ number_format($reception->total, 2, ".", " ") }}</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.create_exit') }}" class="btn btn-secondary">Cancelar</a>
                                        <button type="button" class="btn btn-primary btn-update">
                                            <span class="text-update">Guardar </span>
                                            <span class="spinner-border spinner-border-sm text-updating d-none" role="status"
                                                aria-hidden="true"></span>
                                            <span class="ml-25 align-middle text-updating d-none">Guardando...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        </form>
    </section>
@endsection
@section('scripts')
    @include('admin.receptions.register_exit.js-home')
@endsection
</h1>
