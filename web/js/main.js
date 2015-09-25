$( document ).ready(function() {
    $('.btn').click(function()
    {
        var dt = new Date();
        var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
        $.ajax
        ({
            url: 'ajax',
            data: {status: 'send', time: time},
            type: 'post',
            success: function(data)
            {
                $('#show').html(data)
            }
        });
    });
});


