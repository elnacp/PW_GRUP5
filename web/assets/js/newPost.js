// JavaScript Document
"use strict";
$("#files").change(function(){
    if(this.files && this.files[0]) {
        var reader = new FileReader();


                        reader.readAsDataURL(this.files[0]);
                reader.onload = function(e){
                    $("#PostImg").attr('src', e.target.result);
                    $("#files").attr('value', e.target.result);

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


function imageGran() {
    if (document.getElementById("imgPetita")) {
        eliminarElemento("imgPetita");
    }
    if (document.getElementById("imgGran")) {

    }else{
        var imagen = document.createElement("img");
        imagen.setAttribute("src", "~/imagenes/libro.jpg");
        imagen.setAttribute("width", "400");
        imagen.setAttribute("height", "300");
        imagen.setAttribute("id", "imgGran");
        var div = document.getElementById("gran");
        div.appendChild(imagen);
    }
}

function imagePetita() {
    if (document.getElementById("imgGran")) {
        eliminarElemento("imgGran");
    }
    if (document.getElementById("imgPetita")) {
    }else{
        var imagen = document.createElement("img");
        imagen.setAttribute("src", "~/imagenes/libro.jpg");
        imagen.setAttribute("width", "100");
        imagen.setAttribute("height", "100");
        imagen.setAttribute("id", "imgPetita");
        var div = document.getElementById("petita");
        div.appendChild(imagen);
    }
}

function eliminarElemento(id){
    var imagen = document.getElementById(id);
    if (!imagen){
        alert("El elemento selecionado no existe");
    } else {
        var padre = imagen.parentNode;
        padre.removeChild(imagen);
    }
}