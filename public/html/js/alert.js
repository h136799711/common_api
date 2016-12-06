/**
 * Created by 1 on 2016-10-14.
 */
console.log("load by_alert component");
var by_alert = {
    
    props: ['alert_id','alert_msg'],
    template: "#by-alert-template",
    data:function(){

        return {

        }
    },
    method: {
        show: function (delay) {
            delay = parseInt(delay);
            if ($toast.css('display') != 'none') return;

            $toast.fadeIn(100);
            setTimeout(function () {
                $toast.fadeOut(100);
            }, 2000);

        },
        hide: function (delay) {
            delay = parseInt(delay);

            if ($toast.css('display') != 'none') return;

            $toast.fadeIn(100);
            setTimeout(function () {
                $toast.fadeOut(100);
            }, 2000);

        }
    }
};
