$(document).ready(function(){

    $(".fancybox").fancybox({
        openEffect: "none",
        closeEffect: "none",
        padding:0, margin:0,

        beforeShow: function () {
            $("body *:not(.fancybox-overlay, .fancybox-overlay *)").addClass("blur");
        },
        afterClose: function () {
            $("body *:not(.fancybox-overlay, .fancybox-overlay *)").removeClass("blur");
        }

    });
});
