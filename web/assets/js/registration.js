// JavaScript Document
"use strict";


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
    //miramos que no sea año futuro
    if(year > yyyy){
        alert(year + "error, invalid year");
        return false;
    }

    //miramos que el día no sea futuro
    if(year == yyyy && (month == mm) && (day > dd)){
        alert(day + "error, invalid day");
        return false;
    }

    //miramos que el mes no sea futuro
    if(year == yyyy  && (month > mm) && (day <= dd)){
        alert(month + "error, invalid month");
        return false;
    }

    /*if(year <= yyyy <= 0 && (month.localeCompare(mm) == 1) && (day.localeCompare(dd) == 1)){
        alert(month + "error, invalid date");
        return false;
    }*/
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

    var nickname = $('#New_nickname').val();
    var email = $('#New_email').val();
    var age = $('#New_edad').val();
    var pass = $('#New_pass').val();
    var repeat = $('#sure').val();


    var isCorrect = true;

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

function imageGran() {
    /*if (document.getElementById("imgPetita")) {
        eliminarElemento("imgPetita");
    }*/
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
    /*if (document.getElementById("imgGran")) {
        eliminarElemento("imgGran");
    }*/
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

