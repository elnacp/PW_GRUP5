// JavaScript Document
"use strict";

    console.log("hola");
    var img = document.getElementById("imageid");
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    var dataURL = canvas.toDataURL("image/jpeg");
    $("#ImPostAcctual").attr('value', dataURL);


$("#files").change(function(){
    if(this.files && this.files[0]) {
        var reader = new FileReader();

        reader.readAsDataURL(this.files[0]);
        reader.onload = function(e){
            $("#PostImg").attr('src', e.target.result);
            $("#PostImg").attr('class', 'image-responsive');
            $("#files").attr('value', e.target.result);
            $("#profilePic").attr('src', e.target.result);
        }
    }
});

function archivo(evt) {
    var files = evt.target.files; // FileList object

    // Obtenemos la imagen del campo "file".
    for (var i = 0, f; f = files[i]; i++) {
        //Solo admitimos imágenes.
        if (!f.type.match('image.*')) {
            continue;
        }

        var reader = new FileReader();

        reader.onload = (function(theFile) {
            return function(e) {
                // Insertamos la imagen
                document.getElementById("list").innerHTML = ['<img class="thumb" src="', e.target.result,'" title="', escape(theFile.name), '"/>'].join('');
            };
        })(f);

        reader.readAsDataURL(f);
    }
}

document.getElementById('files').addEventListener('change', archivo, false);


$(document).ready(function(){
    $('input[name="size1"]').change(function(){
        if($('#imageGran').prop('checked')){
            document.getElementById("imagePetita").checked = false;        }
    });
});

$(document).ready(function(){
    $('input[name="size2"]').change(function(){
        if($('#imagePetita').prop('checked')){
            document.getElementById("imageGran").checked = false;
        }
    });
});
