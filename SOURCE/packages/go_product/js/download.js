// TODO: FOR DELETION (ANZGO-3872)
// ANZGO-3872 tagged by scamus 20181010

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var milisec=0 ;

var seconds=6 ;

$(document).ready(function(){

    console.log('run downloading');
 
    $('#d2').val('6'); 

    $('.close_window').hide();

    display();

});

function display(){

    $('.close_window').hide();

    if (milisec<=0){ 
        milisec=9 
        seconds-=1 
    } 
    if (seconds<=-1){
        milisec=0 
        seconds+=1 
    }else{
        milisec-=1 
        $('.d2').val(seconds);
        if(seconds == 0){
            $('.counter').hide();
            $('.close_window').show();
            window.location.href = download_file_url;
            return false;
        }

       setTimeout("display()",100) 
    }
    
}