@extends('admin.template')
@section('styles')
    <style>
        body
        {overflow-x:hidden;}
    </style>
@endsection
@section('content')


    <section class="basic-select2">
        <div class="row">
            <!-- Congratulations Card -->
            <div class="col-12 col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Datos de la Empresa</h5>
                        <form id="form-info" class="form form-vertical">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="ruc">NIT</label>
                                    <input type="text" id="ruc" class="form-control" name="ruc"
                                        value="{{ $business->ruc }}" />
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="razon_social">Nombre de Empresa</label>
                                    <input type="text" id="razon_social" class="form-control text-uppercase"
                                        name="razon_social" value="{{ $business->razon_social }}" />
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="direccion">Direcci&oacute;n</label>
                                    <input type="text" id="direccion" class="form-control text-uppercase"
                                        name="direccion" value="{{ $business->direccion }}" />
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="pais">Pa&iacute;s</label>
                                    <select name="pais" id="pais" class="form-control">
                                        <option value="PE">Guatemala</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="pais">Departamento</label>
                                    <select name="pais" id="pais" class="form-control">
                                        <option value="PE">Quiché</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="pais">Municipio</label>
                                    <select name="pais" id="pais" class="form-control">
                                        <option value="PE">Zacualpa</option>
                                    </select>
                                </div>

                        

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="email_accounting">Contacto Contabilidad</label>
                                    <input type="text" id="email_accounting" class="form-control" name="email_accounting"
                                        value="{{ $business->email_accounting }}" />
                                </div>

                                <div class="col-12 text-end mb-2">
                                    <button type="button" class="btn btn-primary btn-save-info">
                                        <span class="text-save-info">Guardar</span>
                                        <span class="spinner-border spinner-border-sm me-1 d-none text-saving-info" role="status" aria-hidden="true"></span>
                                        <span class="text-saving-info d-none">Guardando...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--/ Congratulations Card -->
      <!-- Medal Card -->
            

</div>
</form>
</div>
</div>
</div>
<!--/ Medal Card -->
</div>
</section>
@endsection
@section('scripts')
    @include('admin.business.js-home')
@endsection
