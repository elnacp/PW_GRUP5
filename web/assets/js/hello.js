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


        /*<a  href=" . $href1 . " class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>*/
        success: function (response) {
            console.log(response);
            totsPost = response;
            var logejat = $('.logejat').attr('id');
            console.log("logejat" +logejat);

            //var div = document.getElementById('comentaris');




            for (var i = 0; i < totsPost['info'].length && i < 5; i++) {

                postVisibles.push(totsPost['info'][index]);
                //console.log(postVisibles[index]['img_id']);
                var div2 = document.createElement('div');
                div2.setAttribute("class", "[ panel panel-default ] panel-google-plus");
                var div3 = document.createElement('div');
                div3.setAttribute("class", "panel-heading");
                var h2 = document.createElement('h2');
                var a1 = document.createElement('a');
                var href2 = "/visualitzacioImatge/" + postVisibles[index]['img_id'];
                a1.setAttribute("href", href2 );
                a1.innerHTML = postVisibles[index]['titol'];
                h2.appendChild(a1);
                div3.appendChild(h2);
                var h3 = document.createElement('h3');
                var a2 = document.createElement('a');
                var href3 = "/perfil/" + postVisibles[index]['autor'];
                a2.setAttribute("href", href3 );
                a2.innerHTML = postVisibles[index]['autor'];
                h3.appendChild(a2);
                div3.appendChild(h3);
                var h5 = document.createElement('h5');
                var span = document.createElement('span');
                span.innerHTML = "Publicat - " + postVisibles[index]['publicat'] ;
                h5.appendChild(span);
                div3.appendChild(h5);
                var img = document.createElement('img');
                img.setAttribute("class", "img-circle" );
                img.setAttribute("src",postVisibles[index]['img_perfil']);
                img.setAttribute("alt", "User Image");
                div3.appendChild(img);


                var img_post = document.createElement('img');
                img_post.setAttribute("class", "img-thumbnail img-responsive center-block" );
                img_post.setAttribute("id", "imgPost");
                img_post.setAttribute("src",postVisibles[index]['img_path']);
                img_post.setAttribute("alt", "User Image");

                div2.appendChild(div3);
                div2.appendChild(img_post);


                var div4 = document.createElement('div');
                div4.setAttribute("class", "panel-footer");

                var a = document.createElement('a');
                if(logejat == 1) {
                    var href4 = "/likeHome/" + postVisibles[index]['img_id'] + "/"+postVisibles[index]['autor'];
                    a.setAttribute("href", href4);
                    //<a  href=" . $href1 . " class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>"

                }

                a.setAttribute("class", "[ btn btn-default ]");
                a.innerHTML = "Likes: + " +postVisibles[index]['likes'];
                div4.appendChild(a);
                var button = document.createElement('button');
                button.innerHTML = "Visitas: +" +postVisibles[index]['visitas'];
                button.setAttribute("class","[ btn btn-default ]" );
                button.setAttribute("type", "button");
                div4.appendChild(button);
                //<div class=\"input-placeholder\">Escribe un comentario...</div>
                var divaux = document.createElement('div');
                divaux.setAttribute("class", "input-placeholder");
                divaux.innerHTML = "Escribe un comentario...";
                div4.appendChild(divaux);
                div2.appendChild(div4);

                if(logejat == 1) {
                    console.log("ENTRA");

                    var div5 = document.createElement('div');
                    div5.setAttribute("class", "panel-google-plus-comment");
                    var div6 = document.createElement('div');
                    div6.setAttribute("class", "panel-google-plus-textarea");
                    var form = document.createElement('form');
                    var href = "/comentari/" + postVisibles[index]['img_id'] + "/" + postVisibles[index]['autor'];
                    form.setAttribute("action", href);
                    form.setAttribute("method", "POST");
                    var textarea = document.createElement('textarea');
                    textarea.setAttribute("rows", 4);
                    textarea.setAttribute("name", "comentari");
                    form.appendChild(textarea);
                    var button = document.createElement('button');
                    button.innerHTML = "Comentar";
                    button.setAttribute("class", "[ btn btn-success disabled ]");
                    button.setAttribute("type", "button");
                    form.appendChild(button);
                    div6.appendChild(form);
                    var button = document.createElement('button');
                    button.innerHTML = "Cancelar";
                    button.setAttribute("class", "[ btn btn-default ]");
                    button.setAttribute("type", "reset");
                    div6.appendChild(button);
                    div5.appendChild(div6);
                    var div7 = document.createElement('div');
                    div7.setAttribute("class", "clearfix");
                    div5.appendChild(div7);
                    div2.appendChild(div5);

                }



                $(div2).insertBefore("#loadmore");






                index++;
            }

        }
    });

    $('#loadmore').click(function(){
        console.log('hola');
        console.log(index);

        if( totsPost.length == postVisibles.length && postVisibles.length != 0){
            alert('Tots carregats');
            return;
        }else{
            //var div = document.getElementById('contenidorComentaris');
            for( var i = 0; i < 5 && index < totsPost.length; i++){


            }

        }
    });


};

/*console.log("hello");
 var div2 = document.createElement('div');
 div2.setAttribute("class", "[ panel panel-default ] panel-google-plus");
 var div3 = document.createElement('div');
 div3.setAttribute("class", "panel-heading");
 var h2 = document.createElement('h2');
 var href2 = "/visualitzacioImatge/" + postVisibles[index]['img_id'];
 h2.setAttribute("href", href2 );
 h2.innerHTML = postVisibles[index]['titol'];
 div3.appendChild(h2);
 var h3 = document.createElement('h3');
 var href3 = "/perfil/" + postVisibles[index]['autor'];
 h3.setAttribute("href", href3 );
 div3.appendChild(h3);
 var h5 = document.createElement('h5');
 var span = document.createElement('span');
 span.innerHTML = "Publicat" .postVisibles[index]['publicat'] ;
 h5.appendChild(span);
 div3.appendChild(h5);
 var img = document.createElement('img');
 img.setAttribute("class", "img-circle" );
 img.setAttribute("src",postVisibles[index]['img_perfil']);
 div3.appendChild(img);
 div2.appendChild(div3);

 var img_post = document.createElement('img');
 img_post.setAttribute("class", "img-thumbnail img-responsive center-block" );
 img_post.setAttribute("id", "imgPost");
 img.setAttribute("src",postVisibles[index]['img_path']);
 div2.appendChild(img_post);

 div.appendChild(div2);*/

/*if(logejat == true){
 var a = document.createElement('a');
 var href4 = "/likeHome/" + postVisibles[index]['img_id'] +  "/" . postVisibles[index]['autor'];
 }*/
