// JavaScript Document
"use strict";

function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    var x = document.createElement("IMG");
    x.setAttribute('id','profile');
    for (var i = 0, f; f = files[i]; i++) {
        //output.push('<img class ="img-thumbnail img-responsive" src=', URL.createObjectURL(evt.target.files[i]), '>');
        x.setAttribute('src', URL.createObjectURL(event.target.files[i]));
        x.setAttribute('name', 'image');
        x.setAttribute('class','img-thumbnail img-responsive');
        var aux = document.createElement("INPUT");
        aux.setAttribute('name', "imgP");
        aux.setAttribute('value',URL.createObjectURL(event.target.files[i]));
    }
    document.getElementById('registerImg').appendChild(x);
    document.getElementById('registerImg').appendChild(aux);
}
document.getElementById('files').addEventListener('change', handleFileSelect, false);
/**
 * Created by noa on 24/4/17.
 */
