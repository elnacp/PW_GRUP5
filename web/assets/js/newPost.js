// JavaScript Document
"use strict";
$("#files").change(function(){
    if(this.files && this.files[0]){
        var reader = new FileReader();

        reader.readAsDataURL(this.files[0]);
        reader.onload = function(e){
            $("#PostImg").attr('src', e.target.result);
            $("#files").attr('value', e.target.result);

        }
    }
});
