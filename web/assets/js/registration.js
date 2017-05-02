// JavaScript Document
"use strict";
function saveBlobAsFile(blob, fileName) {

    var reader = new FileReader();

    reader.onloadend = function () {
        var base64 = reader.result ;
        var link = document.createElement("a");

        link.setAttribute("href", base64);
        link.setAttribute("download", fileName);
        link.click();
    };

    reader.readAsDataURL(blob);
}

$("#files").change(function(){
    if(this.files && this.files[0]){
        var reader = new FileReader();

        reader.readAsDataURL(this.files[0]);
        reader.onload = function(e){
            $("#profilePic").attr('src', e.target.result);
            $("#files").attr('value', e.target.result);
        }
    }


});

/*function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    var x = document.createElement("IMG");
    x.setAttribute('id','profile');
    /*for (var i = 0, f; f = files[i]; i++) {
        //output.push('<img class ="img-thumbnail img-responsive" src=', URL.createObjectURL(evt.target.files[i]), '>');
        var reader =  new FileReader();
        x.setAttribute('src',reader.readAsDataURL(evt.target.files[i]));
        x.setAttribute('name', 'image');
        x.setAttribute('download');
        x.setAttribute('class','img-thumbnail img-responsive');
        var aux = document.createElement("INPUT");
        aux.setAttribute('name', "imgP");
        aux.setAttribute('value',reader.readAsDataURL(event.target.files[i]));

    }
    document.getElementById('registerImg').appendChild(x);
    document.getElementById('registerImg').appendChild(aux);
}
document.getElementById('files').addEventListener('change', handleFileSelect, false);*/


function valName(name){
    return (name.length > 20)
}

function valApellido(apellido){
    return (apellido.length > 20)
}

function valNickname(nickname){
    return (nickname.length > 20)
}

function valEmail(email){
    var pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return pattern.test(email);
}

function valAge(age){
    var fields = age.split('-');

    var year = fields[0];
    var month = fields[1];
    var day = fields[2];

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yyyy = today.getFullYear();

    if(dd<10) {
        dd='0'+dd
    }

    if(mm<10) {
        mm='0'+mm
    }

    if(year.localeCompare(yyyy) == 1){
        alert(year + "error, invalid date");
        return false;
    }

    if(year <= yyyy && (month.localeCompare(mm) <= 0) && (day.localeCompare(dd) == 1)){
        alert(day + "error, invalid date");
        return false;
    }

    if(year <= yyyy <= 0 && (month.localeCompare(mm) == 1) && (day.localeCompare(dd) <= 0)){
        alert(month + "error, invalid date");
        return false;
    }

    if(year <= yyyy <= 0 && (month.localeCompare(mm) == 1) && (day.localeCompare(dd) == 1)){
        alert(month + "error, invalid date");
        return false;
    }
    return true;




}

function valPassword(password){
    var valid = false;
    var num, min, may = false;
    if(password.length >= 6 && password.length< 13){
        var TCode = password.value;
        if (/[a-z]/.test(password)){
            min = true;
        }
        if (/[A-Z]/.test(password)){
            may = true;
        }
        if (/[0-9]/.test(password)){
            num = true;
        }

        if (num && min && may){
            valid = true;
        }
    }
    return valid;
}

$('#registro').submit(function(event) {


   // var name = $('#New_nombre').val();
    //var f_name = $('#New_apellido').val();
    var nickname = $('#New_nickname').val();
    var email = $('#New_email').val();
    var age = $('#New_edad').val();
    var pass = $('#New_pass').val();
    var repeat = $('#sure').val();


    var isCorrect = true;

   /* if(valName(name)){
        alert("ERROR! Nombre no válido!");
        isCorrect = false;
    }

    if(valApellido(f_name)) {
        alert("ERROR! Apellido no válido!");
        isCorrect = false;
    }*/

    if(valNickname(nickname)){
        alert("ERROR! Nickname no válido!");
        isCorrect = false;
    }

    if(!valEmail(email)){
        alert("ERROR! Email no válido!");
        isCorrect = false;
    }

    if(!valAge(age)){
        alert("ERROR!  no válido!");
        isCorrect = false;
    }

    if (pass != repeat){
        alert("ERROR! Validación de contraseña incorrecta.");
        isCorrect = false;

    } else if (!valPassword(pass)){
        alert("ERROR! Contraseña no válida!");
        isCorrect = false;
    }


    if(!isCorrect){
        event.preventDefault();
    }else{

    }

});

