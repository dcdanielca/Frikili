function like(id){
    $.ajax({
        type: "POST",
        url: '/like',
        data: {'id': id},
        asyn: true,
        dataType: "json",
        success: function (response) {
            $('.likeuser').text('Te gustó esto')
        }
    });
}