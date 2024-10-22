<script>
    function to_pay() {
        let precio      = parseFloat($('input[name="precio"]').val()).toFixed(2),
            adelanto    = parseFloat($('input[name="adelanto"]').val()).toFixed(2),
            mora        = parseFloat($('input[name="mora"]').val()).toFixed(2),
            diferencia  = parseFloat($('input[name="diferencia"]').val()).toFixed(2);
            $('input[name="diferencia"]').val(((parseFloat(precio) - parseFloat(adelanto)) + parseFloat(mora)).toFixed(2));
    }
    to_pay();

    $('body').on('change', '.mora', function() {
        let mora = $(this).val();

        if (mora.trim() == '') {
            return;
        }
        if (isNaN(mora)) {
            toast_msg('Solo se permiten números', 'warning');
            $(this).focus();
            return;
        }

        $('input[name="mora"]').val(parseFloat(mora).toFixed(2)); 
        to_pay();
        calculate__totals();
    });


    function calculate__totals() {
        var exonerada   = 0,
            gravada     = 0,
            inafecta    = 0,
            codigo_igv  = 0;
            subtotal    = 0,
            total       = 0,
            impuesto    = 0,
            cantidad    = 0,
            igv         = 0,
            diferencia  = parseFloat($('input[name="diferencia"]').val());

        $('#wrapper-tbody tr').each(function() {
            let idproducto      = $(this).find('td:eq(4)').data('id'),
                impuesto        = $(this).find('td:eq(4)').data('impuesto'),
                codigo_igv      = $(this).find('td:eq(4)').data('codigo_igv'),
                pagado          = parseInt($(this).find('td:eq(4)').data('pagado')),
                cantidad        = $(this).find('td:eq(3)').text();
        
            if(pagado == 0) {
                if (impuesto == 1) {
                    igv += ((parseFloat($(this).find('td:eq(4)').text())) - (parseFloat($(this)
                        .find('td:eq(4)').text()) / 1.18) * parseInt(cantidad));
                    igv = redondeado(igv);
                }

                if (codigo_igv == 10) {
                    gravada += (parseFloat($(this).find('td:eq(4)').text()) / 1.18) * parseInt(
                        cantidad);
                    gravada = redondeado(gravada);
                }

                if (codigo_igv == 20) {
                    exonerada += (parseFloat($(this).find('td:eq(4)').text())) * parseInt(
                        cantidad);
                    exonerada = redondeado(exonerada);
                }

                if (codigo_igv == 30) {
                    inafecta += (parseFloat($(this).find('td:eq(4)').text())) * parseInt(cantidad);
                    inafecta = redondeado(inafecta);
                }

                subtotal = exonerada + gravada + inafecta;
            }
        });

        total = subtotal + igv + diferencia;
        $('.span__exonerada').text(parseFloat(exonerada).toFixed(2));
        $('.span__gravada').text(parseFloat(gravada).toFixed(2));
        $('.span__inafecta').text(parseFloat(inafecta).toFixed(2));
        $('.span__subtotal').text(parseFloat(subtotal).toFixed(2));
        $('.span__igv').text(parseFloat(igv).toFixed(2));
        $('.span__total').text(parseFloat(total).toFixed(2));
    }

    calculate__totals();

    function redondeado(numero, decimales = 2) {
        let factor = Math.pow(10, decimales);
        return (Math.round(numero * factor) / factor);
    }

    $('body').on('click', '.btn-update', function() {
        event.preventDefault();
        let productos = [],
            totales = null,
            observaciones = $('textarea[name="observaciones"]').val(),
            idrecepcion = $('input[name="idrecepcion"]').val(),
            precio_total = $('input[name="precio"]').val(),
            mora         = $('input[name="mora"]').val(),
            diferencia   = $('input[name="diferencia"]').val(),
            idroom   = $('input[name="idroom"]').val();
            

        $('#wrapper-tbody tr').each(function() {
            let nuevo_producto = {
                idproducto      : $(this).find('td:eq(4)').data('id'),
                pagado          : parseInt($(this).find('td:eq(4)').data('pagado')),
                cantidad        : $(this).find('td:eq(3)').text(),
                precio          : $(this).find('td:eq(4)').text()
            }
            productos.push(nuevo_producto);
        });

        let suma_totales = {
            exonerada: $('.span__exonerada').text(),
            gravada: $('.span__gravada').text(),
            inafecta: $('.span__inafecta').text(),
            subtotal: $('.span__subtotal').text(),
            igv: $('.span__igv').text(),
            total: $('.span__total').text()
        }
        totales = suma_totales;

        Swal.fire({
            title: 'Confirmar Salida',
            text: "¿Desea confirmar la salida?",
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
                $.ajax({
                    url: "{{ route('admin.gen_reception_exit') }}",
                    method: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'productos': JSON.stringify(productos),
                        'totales': JSON.stringify(totales),
                        observaciones: observaciones,
                        idrecepcion: idrecepcion,
                        precio_total : precio_total,
                        mora: mora,
                        diferencia: diferencia,
                        idroom: idroom
                    },
                    beforeSend: function() {
                        $('.btn-update').prop('disabled', true);
                        $('.text-update').addClass('d-none');
                        $('.text-updating').removeClass('d-none');
                    },
                    success: function(r) {
                        if (!r.status) {
                            $('.btn-update').prop('disabled', false);
                            $('.text-update').removeClass('d-none');
                            $('.text-updating').addClass('d-none');
                            toast_msg(r.msg, r.type);
                            return;
                        }

                        $('.btn-update').prop('disabled', false);
                        $('.text-update').removeClass('d-none');
                        $('.text-updating').addClass('d-none');
                        Swal.fire({
                            icon: 'success',
                            title: r.msg,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('admin.create_reception') }}"
                        })
                    },
                    dataType: 'json'
                });
            }
        });
    });
</script>