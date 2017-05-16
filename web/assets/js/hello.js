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

        var val = $('.final').attr('val');
        $.ajax({
            type: "POST",
            url: '/afegir',

            success: function (response){
                if(!isFinite(response))
                {
                    $('.final').remove();
                    $(response).insertBefore('.loadmore');
                }
                else
                {
                    $('<div class="well">No more feeds</div>').insertBefore('.loadmore');
                    $('.loadmore').remove();
                }//console.log(response);

            }
        });
    });
});



