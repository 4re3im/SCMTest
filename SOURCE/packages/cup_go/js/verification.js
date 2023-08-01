/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    
    $('#continue').click(function(){
        
        if($('#acceptterms').attr('checked')=='checked'){
            
            // Create a formdata object and add the files
            var data = new FormData();

            data.append('user_id', user_id);

            data.append('password', $('#password').val());

            data.append('allow_marketing_contact', $('#AllowMarketingContact').attr('checked')=='checked' ? 'NO' : 'YES');

            $.ajax({

                url: verify_account, type: 'POST', data: data, cache: false,

                dataType: 'json', processData: false, contentType: false,

                success: function(data){

                    $('#newuser').html('Your account was successfully activated.');

                } 

            });

        }else{

            alert('You need to accept Terms of Use to continue.');
            
            return false;

        }
        
    });
    
    var typingTimer; //timer identifier

    var doneTypingInterval = 300;  //time in ms, 5 second for example   

    $("#verifypassword").on("keyup", function(e){

        typingTimer = setTimeout(function(){

            if($('#password').val()!=$('#verifypassword').val()){
                
                $('#verifypassword_message_div').html('Password not match');
                
            }else{
                
                $('#verifypassword_message_div').html('');
                
            }

        }, doneTypingInterval );

    });
 
});