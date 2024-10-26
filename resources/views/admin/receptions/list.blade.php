@extends('admin.template')
@section('content')
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="card-title mb-0">Gesti&oacute;n de Recepciones</h5>
                    </div>
                    <a href="{{ route('admin.create_reception') }}" class="dt-button create-new btn btn-primary waves-effect waves-light">
                        <span><i class="ti ti-plus me-sm-1"></i><span class="d-none d-sm-inline-block">Nuevo</span></span>
                    </a>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table id="table" class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">Habitaci&oacute;n</th>
                                    <th width="10%">NIT</th>
                                    <th class="text-left">Cliente</th>
                                    <th width="10%">Entrada</th>
                                    <th width="10%">Salida</th>
                                    <th width="10%">Estado</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('admin.receptions.js-datatable')
    @include('admin.receptions.js-store')
@endsection