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

window.onload = function() {

    var params = {
        'id': $('.image_id').attr('id')
    };

    console.log( $('.image_id').attr('id'));

    $.ajax({
        data: params,
        type: "POST",
        url: '/afegir',

        success: function (response) {
            totsComentaris = response;

            var div = document.getElementById('contenidorComentaris');

            for (var i = 0; i < totsComentaris.length && i < 3; i++) {
            /*<div class="panel-heading">*/
                comentarisVisibles.push(totsComentaris[index]);
                var contenidor = document.createElement('div');
                contenidor.setAttribute("class", "panel-heading");
                var titol = document.createElement('h2');
                titol.innerHTML = comentarisVisibles[index]['autor'];

                var p = document.createElement('p');
                p.innerHTML = comentarisVisibles[index]['comentari'];
                contenidor.appendChild(titol);
                contenidor.appendChild(p);
                div.appendChild(contenidor);
                index++;
            }
        }
    });

    $('#loadmore').click(function(){


        if( totsComentaris.length == comentarisVisibles.length && comentarisVisibles.length != 0){
            alert('Tots carregats');
            return;
        }else{
            var div = document.getElementById('contenidorComentaris');
            for( var i = 0; i < 3 && index < totsComentaris.length; i++){

                comentarisVisibles.push(totsComentaris[index]);
                var contenidor = document.createElement('div');
                contenidor.setAttribute("class", "panel-heading");
                var titol = document.createElement('h2');
                titol.innerHTML = comentarisVisibles[index]['autor'];

                var p = document.createElement('p');
                p.innerHTML = comentarisVisibles[index]['comentari'];
                contenidor.appendChild(titol);
                contenidor.appendChild(p);
                div.appendChild(contenidor);
                index++;

            }

        }
    });

};


