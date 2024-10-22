<script>
    function load_rooms() {
        $.ajax({
            url: "{{ route('admin.load_rooms_exit') }}",
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
    
    $('document').ready(function(){
        load_navs();
        load_rooms();
    });
</script>