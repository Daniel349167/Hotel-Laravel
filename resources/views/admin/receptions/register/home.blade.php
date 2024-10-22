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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="form-save-reception" class="form form-vertical">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <h5 class="card-title text-primary fw-bold mb-4">Detalles de la Habitaci&oacute;n</h5>
                                    <div class="table-responsive-sm">
                                        <table class="table table-hover table-sm">
                                            <tbody>
                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Nombre</th>
                                                    <td colspan="1" class="td__dispatch">{{ $room->descripcion }}</td>
                                                </tr>

                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Tipo Habitaci&oacute;n</th>
                                                    <td colspan="1" class="td__receiver">{{ $room->categoria }}</td>
                                                </tr>
    
                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        Detalle</th>
                                                    <td colspan="1" class="td__dispatch">{{ $room->detalle }}</td>
                                                </tr>

                                                <tr class="el-descriptions-row" style="border: 1px solid #EBEEF5;">
                                                    <th colspan="1"
                                                        style="font-weight: normal; color: #64666b; background: #fafafa;">
                                                        NIVEL</th>
                                                    <td colspan="1" class="td__receiver">{{ $room->sala }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 col-md-7">
                                        <h5 class="card-title text-primary fw-bold mb-3">Detalle Cliente</h5>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <input type="hidden" name="idroom" value="{{ $room->id }}">
                                                <label class="form-label" for="dni_ruc">Cliente</label>
                                                <small class="text-primary fw-bold btn-create-client" style="cursor: pointer">[+
                                                    Nuevo]</small>
                                                <select class="select2-size-sm form-control" id="dni_ruc" name="dni_ruc">
                                                    <option></option>
                                                    @foreach ($clients as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->dni_ruc . ' - ' . $client->nombres }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
            
                                            <div class="col-12 mb-3">
                                                <h5 class="card-title text-primary fw-bold mt-2">Detalle Reservaci&oacute;n</h5>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="fecha_entrada">Fecha Entrada</label>
                                                            <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="fecha_salida">Fecha Salida</label>
                                                            <input type="date" name="fecha_salida" id="fecha_salida" class="form-control" value="{{ date('Y-m-d') }}">
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="precio">Precio</label>
                                                            <input type="text" name="precio" id="precio" class="form-control" value="{{ $room->precio }}" readonly>
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="adelanto">Adelanto</label>
                                                            <input type="text" name="adelanto" id="adelanto" class="form-control adelanto" value="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="diferencia">Diferencia</label>
                                                            <input type="text" name="diferencia" id="diferencia" class="form-control" value="0.00">
                                                        </div>
                                                    </div>
            
                                                    <div class="col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="observacion">Observaci&oacute;n</label>
                                                            <textarea name="observacion" id="observacion" cols="8" rows="3" class="form-control text-uppercase"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
            
                                        <div class="col-12 text-end mb-2">
                                            <a href="{{ route('admin.create_reception') }}" class="btn btn-secondary">Cancelar</a>
                                            <button type="button" class="btn btn-primary btn-save">
                                                <span class="text-save">Guardar </span>
                                                <span class="spinner-border spinner-border-sm text-saving d-none" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="ml-25 align-middle text-saving d-none">Guardando...</span>
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--/ Congratulations Card -->

            
        </div>
        </div>
    </section>

    @include('admin.clients.modal-register')
@endsection
@section('scripts')
    @include('admin.clients.js-register')
    @include('admin.receptions.register.js-home')
@endsection
</h1>
