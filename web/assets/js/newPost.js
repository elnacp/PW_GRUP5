// JavaScript Document
"use strict";

function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    for (var i = 0, f; f = files[i]; i++) {
        output.push('<img src=', URL.createObjectURL(event.target.files[i]), '>');
    }
    document.getElementById('registerImg').innerHTML =  output.join('');
    //document.getElementById('profilePic').style.width = "50px";
}
document.getElementById('files').addEventListener('change', handleFileSelect, false);
/**
 * Created by noa on 24/4/17.
 */
