function like(id){
    $.ajax({
        type: "POST",
        url: '/like',
        data: {'id': id},
        asyn: true,
        dataType: "json",
        success: function (response) {
            $('.likeuser').text('Te gustó esto');
            var numLikes = response.likes.split(',').length - 1;
            $('.likesPost').html(numLikes + ' Likes');
        }
    });
}