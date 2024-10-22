<script>
    function load_rooms() {
        $.ajax({
            url: "{{ route('admin.load_rooms') }}",
            method: "POST",
            data: {
                '_token': "{{ csrf_token() }}"
            },
            success: function(r) {
                if (!r.status) {
                    toast_msg(r.msg, r.type);
                    return;
                }
                $('#wrapper_rooms').html(r.html);
            },
            dataType: "json"
        });
    }

    function load_navs()
    {
        $('.nav-link').each(function(){
            var currElem = $(this);
            if(currElem.data('id') == 1)
            {
                currElem.addClass('active');
            }
            else {
                currElem.removeClass('active');
            }
        });  
    }

    $('body').on('click', '.btn-valid-room', function() {
        event.preventDefault();
        let status  = parseInt($(this).data('status')),
            id      = $(this).data('id');
        if(status == 3) {
            Swal.fire({
                title: 'Habilitar',
                text: "¿Habilitar Habitación?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, habilitar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) 
                {
                    $.ajax({
                        url         : "{{ route('admin.enable_room') }}",
                        method      : 'POST',
                        data        : {
                            '_token': "{{ csrf_token() }}",
                            id      : id
                        },
                        success     : function(r){
                            if(!r.status)
                            {
                                toast_msg(r.msg, r.type);
                                return;
                            }

                            toast_msg(r.msg, r.type);
                            load_navs();
                            load_rooms();
                        },
                        dataType    : 'json'
                    });
                }
            });
            return;
        }   

        window.location.href = route('admin.register_reception', id);
    });
    
    $('document').ready(function(){
        load_navs();
        load_rooms();
    });
</script>