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
                                            <input type="hidden" name="fecha_entrada" value="{{ $reception->fecha_entrada }}">
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ $reception->total }}" readonly name="precio">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Adelanto</label>
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ $reception->adelanto }}" readonly name="adelanto">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Diferencia</label>
                                            <input type="text" id="multicol-first-name" class="form-control" value="{{ $reception->diferencia }}" readonly name="diferencia">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="multicol-first-name">Fecha Salida</label>
                                            <input type="date" id="multicol-first-name" class="form-control" value="{{ $reception->fecha_salida }}" name="fecha_salida">
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
                                                    <th class="text-center" width="10%">Pagado</th>
                                                    <th class="text-right" width="5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="wrapper-tbody">
                                                @foreach ($detail as $i => $product)
                                                <tr id="tr__product__{{ $product['idproducto'] }}">
                                                    <td class="d-none"><input type="hidden" name="idproducto" value="{{ $product['idproducto'] }}"></td>
                                                    <td class="text-left">{{ $product['producto'] }}</td>
                                                    <td class="text-center d-none">{{ $product['unidad'] }}</td>
                                                    <td class="text-right">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text btn-down" style="cursor: pointer;" data-id="{{ $product['idproducto'] }}" data-cantidad="{{ $product['cantidad'] }}" data-precio="{{ number_format($product['precio_unitario'], 2, '.', '') }}" data-idrecepcion="{{ $idrecepcion }}"><i class="ti ti-minus me-sm-1"></i></span>
                                                            <input type="text" data-id="{{ $product['idproducto'] }}" class="quantity-counter text-center form-control" value="{{ intval($product['cantidad']) }}" data-idrecepcion="{{ $idrecepcion }}" name="input-cantidad">
                                                            <span class="input-group-text btn-up" style="cursor: pointer;" data-id="{{ $product['idproducto'] }}" data-cantidad="{{ $product['cantidad'] }}" data-precio="{{ number_format($product['precio_unitario'], 2, '.', '') }}" data-idrecepcion="{{ $idrecepcion }}"><i class="ti ti-plus me-sm-1"></i></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><input type="text" class="form-control form-control-sm text-center" value="{{ number_format($product['precio_unitario'], 2, '.', '') }}" data-cantidad="{{ $product['cantidad'] }}" data-codigo_igv="{{ $product['codigo_igv'] }}" data-impuesto="{{ $product['impuesto'] }}" data-id="{{ $product['idproducto'] }}" data-idrecepcion="{{ $idrecepcion }}" name="input-precio"></td>

                                                    <td class="text-center">{{ number_format(($product["precio_unitario"] * $product["cantidad"]), 2, ".", "") }}</td>

                                                    <td class="text-center"><input class="form-check-input" type="checkbox" name="pagado" {{ ($product["pagado"] == 1) ? "checked" : "" }}></td>
                                                    <td class="text-center"><span data-id="{{ $product['idproducto'] }}" class="text-danger btn-delete-product" data-idrecepcion="{{ $idrecepcion }}" style="cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x align-middle mr-25"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-6 d-flex align-items-end mt-2">
                                    <div class="form-group">
                                        <button type="button"
                                            class="btn btn-primary btn-add-product waves-effect waves-float waves-light"
                                            data-repeater-create="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-plus mr-25">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                            <span class="align-middle">Agregar Producto</span>
                                        </button>
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
                                                    <span class="fw-medium">Q <span class="span__subtotal">{{ number_format(($reception->exonerada + $reception->gravada + $reception->inafecta), 2) }}</span> </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="w-px-100">IGV:</span>
                                                    <span class="fw-medium">Q<span class="span__igv">{{ number_format(($reception->igv), 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="w-px-100">Total (+recepci&oacute;n):</span>
                                                    <span class="fw-medium">Q<span class="span__total">{{ number_format($reception->total, 2, ".", " ") }}</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.create_reception') }}" class="btn btn-secondary">Cancelar</a>
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
        @include('admin.products.modal-add-cart')
        @include('admin.products.modal-register')
    </section>
@endsection
@section('scripts')
    @include('admin.receptions.update.js-home')
    @include('admin.products.js-register')
@endsection
</h1>
