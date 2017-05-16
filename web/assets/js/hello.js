var totsComentaris = new Array();
var comentarisVisibles = new Array();
var index = 0;

$(function () {
    $('.panel-google-plus > .panel-footer > .input-placeholder, .panel-google-plus > .panel-google-plus-comment > .panel-google-plus-textarea > button[type="reset"]').on('click', function(event) {
        var $panel = $(this).closest('.panel-google-plus');
        $comment = $panel.find('.panel-google-plus-comment');

        $comment.find('.btn:first-child').addClass('disabled');
        $comment.find('textarea').val('');

        $panel.toggleClass('panel-google-plus-show-comment');

        if ($panel.hasClass('panel-google-plus-show-comment')) {
            $comment.find('textarea').focus();
        }
    });
    $('.panel-google-plus-comment > .panel-google-plus-textarea > textarea').on('keyup', function(event) {
        var $comment = $(this).closest('.panel-google-plus-comment');

        $comment.find('button[type="submit"]').addClass('disabled');
        if ($(this).val().length >= 1) {
            $comment.find('button[type="submit"]').removeClass('disabled');
        }
    });
});



$(function(){
    $('.loadmore').click(function(){

        if( totsComentaris.length == comentarisVisibles.length && comentarisVisibles != 0){
            alert('Tots carregats');
            return;
        }else{
            for( var i = 0; i < 5 && index <= totsComentaris.length; i++){
                comentarisVisibles.push(totsComentaris(index));
                index++;
            }
        }
        var params = {
            'id': $('.image_id').attr('id')
        };

        $.ajax({
            type: "POST",
            url: '/afegir',
            params: params,
            success: function (response){
                console.log(JSON.parse(response));

            }
        });


    });
});

window.onload({

});


