$(document).ready(function(){
    var count = 1;
    setInterval(function(){
        if(count == 1) {
            location.href = "/go/login/logout/";
        }
        count++;
    },1000);
});
