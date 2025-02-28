<script>
    function load_tbody()
    {
        $.ajax({
            url         : "{{ route('admin.load_tbody_sales') }}",
            method      : "POST",
            data        : {
                '_token': "{{ csrf_token() }}"
            },
            success     : function(r){
                if(!r.status)
                {
                    toast_msg(r.msg, r.type);
                    return;
                }

                $('#wrapper_tbody').html(r.html);
            },
            dataType    : "json"
        });
    }
    load_tbody();

    $('body').on('click', '.btn-send-sunat', function() {
        event.preventDefault();
        block_content('#layout-content');
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.send_bf') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                id: id
            },
            success: function(r) {
                if (!r.status) {
                    close_block('#layout-content');
                    toast_msg(r.msg, r.type);
                    return;
                }

                let ip = r.empresa.url_api,
                    api = "Api/index.php",
                    datosJSON = JSON.stringify(r.data);
                    datosJSON = unescape(encodeURIComponent(datosJSON)),
                    idfactura = parseInt(r.idfactura);
                    send(idfactura, datosJSON, ip, api);
            },
            dataType: 'json'
        });
    });

    function send(idfactura, datosJSON, ip, api) {
        $.ajax({
            url: ip + api,
            method: 'POST',
            data: {
                datosJSON
            },
        }).done(function(res) {
            close_block('#layout-content');
            if (res.trim() == "No se registró") {
                toast_msg('El número de comprobante electrónico esta duplicado, revise la base de datos',
                    'error');
                return;
            }
            let respuesta_sunat = JSON.parse(res),
                estado_conexion = JSON.parse(respuesta_sunat).status,
                cod_respuesta = JSON.parse(respuesta_sunat).codigo_respuesta[0],
                des_respuesta = JSON.parse(respuesta_sunat).des_respuesta[0];

            if (estado_conexion == false) {
                toast_msg("El comprobante no se envió, intente de nuevo", 'warning');
                return;
            }

            if (parseInt(cod_respuesta) == 0) 
            {
                toast_msg(des_respuesta, 'success');
                update_cdr(idfactura);
            }
            load_alerts();
            load_tbody();
        }).fail(function(jqxhr, textStatus, error) {
            let err = textStatus + ", " + error;
            close_block('#layout-content');
            toast_msg("Error al enviar: " + err + '. Consulte con el administrador', 'error');
        });
    }

    function update_cdr(idfactura) {
        let resp = '';
        $.ajax({
            url: "{{ route('admin.update_cdr_bf') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                idfactura: idfactura
            },
            success: function(r) {},
            dataType: 'json'
        });
    }
</script>