// FILE TYPE
$(document).ready(function(){
    $( "#filetype" ).change(function() {
        if($("#filetype option:selected").val() == "null"){
            $(".file").removeClass("in");
            $(".link").removeClass("in");
            $(".html").removeClass("in");
        }
    });
    
    $( "#filetype" ).change(function() {
        if($("#filetype option:selected").val() == "2"){
            $(".link").addClass("in");
            $(".file").removeClass("in");
            $(".html").removeClass("in");
        }
    });
    
    $( "#filetype" ).change(function() {
        if($("#filetype option:selected").val() == "3"){
            $(".html").addClass("in");
            $(".file").removeClass("in");
            $(".link").removeClass("in");
        }
    });
});
// $(document).ready(function(){
//     $( "#filetype" ).change(function() {
//         if($("#filetype option:selected").val() == "1"){
//             $(".file").addClass("in");
//             $(".link").removeClass("in");
//             $(".html").removeClass("in");
//         }
//     });
// });

// TITLE TYPE
$(document).ready(function(){
    $( "#titletype" ).change(function() {
        if($("#titletype option:selected").val() == "null"){
            $(".standalone").removeClass("in");
            $(".partseries").removeClass("in");
            $(".studyguide").removeClass("in");
        }
    });
});
$(document).ready(function(){
    $( "#titletype" ).change(function() {
        if($("#titletype option:selected").val() == "1"){
            $(".standalone").addClass("in");
            $(".partseries").removeClass("in");
            $(".studyguide").removeClass("in");
        }
    });
});
$(document).ready(function(){
    $( "#titletype" ).change(function() {
        if($("#titletype option:selected").val() == "2"){
            $(".partseries").addClass("in");
            $(".standalone").removeClass("in");
            $(".studyguide").removeClass("in");
        }
    });
});
$(document).ready(function(){
    $( "#titletype" ).change(function() {
        if($("#titletype option:selected").val() == "3"){
            $(".studyguide").addClass("in");
            $(".partseries").removeClass("in");
            $(".standalone").removeClass("in");
        }
    });
});
