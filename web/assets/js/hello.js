var totsPost = new Array();
var postVisibles = new Array();
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



window.onload = function() {

    /*var params = {
        'logejat': $('.logejat').attr('id')
    };*/

    //console.log( $('.image_id').attr('id'));

    $.ajax({

        type: "POST",
        url: '/post',

        success: function (response) {
            console.log(response);
            //totsPost = response;

            /*totsComentaris = response;

            var div = document.getElementById('contenidorComentaris');

            for (var i = 0; i < totsComentaris.length && i < 3; i++) {
                comentarisVisibles.push(totsComentaris[index]);
                var titol = document.createElement('h2');
                titol.innerHTML = comentarisVisibles[index]['autor'];

                var p = document.createElement('p');
                p.innerHTML = comentarisVisibles[index]['comentari'];
                div.appendChild(titol);
                div.appendChild(p);
                index++;
            }*/
        }
    });

    $('#loadmore').click(function(){
        console.log('hola');
        console.log(index);

        if( totsPost.length == postVisibles.length && postVisibles.length != 0){
            alert('Tots carregats');
            return;
        }else{
            var div = document.getElementById('contenidorComentaris');
            for( var i = 0; i < 3 && index < totsPost.length; i++){
                /*console.log("helloooo");
                comentarisVisibles.push(totsComentaris[index]);
                var titol = document.createElement('h2');
                titol.innerHTML = comentarisVisibles[index]['autor'];

                var p = document.createElement('p');
                p.innerHTML = comentarisVisibles[index]['comentari'];
                div.appendChild(titol);
                div.appendChild(p);
                index++;*/

            }

        }
    });

};


