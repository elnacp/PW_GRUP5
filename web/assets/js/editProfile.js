// JavaScript Document
"use strict";

function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    for (var i = 0, f; f = files[i]; i++) {
        output.push('<img class ="img-thumbnail img-responsive" src=', URL.createObjectURL(event.target.files[i]), '>');
    }
    document.getElementById('registerImg').innerHTML =  output.join('');
    //document.getElementById('profilePic').style.width = "50px";
}

document.getElementById('files').addEventListener('change', handleFileSelect, false);



function valNickname(nickname){
    return (nickname.length > 20)
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



    var nickname = $('#New_nickname').val();
    var age = $('#New_edad').val();
    var pass = $('#New_pass').val();
    var repeat = $('#sure').val();


    var isCorrect = true;


    if(valNickname(nickname)){
        alert("ERROR! Nickname no válido!");
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

});/**
 * Created by elnacabotparedes on 27/4/17.
 */