var _gaq = _gaq || [];

jQuery(document).ready(function(){

    jQuery("a[gae-category]").each(function(idx, ele){
        var cat = jQuery(ele).attr("gae-category");
        var value = jQuery(ele).attr("gae-value");
        var href = jQuery(ele).attr("href");
        if(value == undefined){
            value = href
        }

        jQuery(ele).click(function(){
            _gaq.push(['_trackEvent',cat, 'Click', value]);
            var flag = false

            window.setTimeout(function(){
                window.location.href = href;
            }, 500);

            var a = jQuery("<div></div>");
            a.css({background: "#333333",
                width: "200px",
                height: "30px",
                color: "#FFFFFF",
                position: "fixed",
                top: "0px",
                left: "0px",
                "text-align": "center",
                "line-height": "30px",
                'z-index': 3000});
            a.text(".. please wait ...");

            jQuery("body").append(a);
            return false;
        });
    });
})