// JavaScript Document
"use strict";



$("#files").change(function(){
    if(this.files && this.files[0]){
        var reader = new FileReader();

        reader.readAsDataURL(this.files[0]);
        reader.onload = function(e){
            $("#newProfilePic").attr('src', e.target.result);
            $("#files").attr('value', e.target.result);

        }
    }
});

function valNickname(nickname){
    return (nickname.length > 20);

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
    var age = $('#New_edad').val();
    var pass = $('#New_pass').val();
    var repeat = $('#sure').val();
    console.log(pass);
    console.log(repeat);

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


    } else{
        if (!valPassword(pass)){
            alert("ERROR! Contraseña no válida!");
            isCorrect = false;
        }
    }


    if(!isCorrect){
        event.preventDefault();
    }else{

    }

});