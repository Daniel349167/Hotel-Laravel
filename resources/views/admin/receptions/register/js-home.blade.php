<script>
    var setTimeOutBuscador = '';

    function open_modal_client() {}

    function success_save_client(msg = null, type = null, idtipocomprobante = null, last_id = null) {
        toast_msg(msg, type);
        load_clients();
        setTimeout(() => {
            $('#form-save-reception select[name="dni_ruc"]').val(last_id);
            $('#form-save-reception select[name="dni_ruc"]').trigger('change');
        }, 500);
    }

    function load_clients() {
        $.ajax({
            url: "{{ route('admin.get_clients_update') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}"
            },
            success: function(r) {
                let html_clients = '<option></option>';
                $.each(r, function(index, client) {
                    html_clients +=
                        `<option value="${client.id}">${client.dni_ruc + ' - ' + client.nombres}</option>`;
                });

                $('#form-save-reception select[name="dni_ruc"]').html(html_clients).select2({
                    placeholder: "[SELECCIONE]"
                });
            },
            dataType: 'json'
        });
        return;
    }

    $('#form-save-reception select[name="dni_ruc"]').select2({
        placeholder: "[SELECCIONE]"
    });

    $('body').on('change', 'input[name="fecha_salida"]', function() {
        event.preventDefault();
        var fecha_inicio = $('input[name="fecha_entrada"]').val();
        var precio = "{{ $room->precio }}";
        var date_1 = new Date(fecha_inicio);
        var date_2 = new Date($(this).val());
        var day_as_milliseconds = 86400000;
        var diff_in_millisenconds = date_2 - date_1;
        var diff_in_days = diff_in_millisenconds / day_as_milliseconds;
        if (diff_in_days < 0) {
            $('input[name="precio"]').val("{{ $room->precio }}");
            toast_msg('La fecha no puede ser anterior', 'warning');
            $('input[name="fecha_salida"]').val("{{ date('Y-m-d') }}");
            return;
        }

        var valor = (diff_in_days == 0) ? 1 : diff_in_days;
        $('input[name="precio"]').val(parseFloat(precio * valor).toFixed(2));
    });

    $('body').on('change', '.adelanto', function() {
        let adelanto = $(this).val(),
            precio = $('input[name="precio"').val();

        if (adelanto.trim() == '') {
            return;
        }

        if (isNaN(adelanto)) {
            toast_msg('Solo se permiten números', 'warning');
            $(this).focus();
            return;
        }


        let diferencia = (parseFloat(precio) - parseFloat(adelanto));
        if (parseFloat(adelanto) > parseFloat(precio)) {
            toast_msg('El adelanto debe ser menor o igual al monto total', 'warning');
            $(this).focus();
            return;
        }
        $(this).val(parseFloat(adelanto).toFixed(2));
        $('input[name="diferencia"]').val(parseFloat(diferencia).toFixed(2));
    });

    $('body').on('click', '#form-save-reception .btn-save', function() {
        event.preventDefault();
        Swal.fire({
            title: 'Confirmar',
            text: "¿Confirmar Recepción?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si, confirmar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                let form = $('#form-save-reception').serialize();
                $.ajax({
                    url: "{{ route('admin.save_reception') }}",
                    method: "POST",
                    data: form,
                    beforeSend: function() {
                        $('#form-save-reception .btn-save').prop('disabled', true);
                        $('#form-save-reception .text-save').addClass('d-none');
                        $('#form-save-reception .text-saving').removeClass('d-none');
                    },
                    success: function(r) {
                        if (!r.status) {
                            $('#form-save-reception .btn-save').prop('disabled', false);
                            $('#form-save-reception .text-save').removeClass('d-none');
                            $('#form-save-reception .text-saving').addClass('d-none');
                            toast_msg(r.msg, r.type);
                            return;
                        }

                        $('#form-save-reception .btn-save').prop('disabled', false);
                        $('#form-save-reception .text-save').removeClass('d-none');
                        $('#form-save-reception .text-saving').addClass('d-none');
                        Swal.fire({
                            icon: 'success',
                            title: r.msg,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('admin.create_reception') }}"
                        });
                    },
                    dataType: "json"
                });
            }
        });
    });
</script>
