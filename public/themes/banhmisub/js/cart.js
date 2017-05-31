var runload;
load_customer_display();
calc_height();
function load_customer_display(){ 
    var count = 1;
    var cycle = 4;
    if($("#hidden_count").val()!=undefined){
        count = parseInt($("#hidden_count").val());
    }
    $.ajax({
            url: "/carts/customer-display",
            method: "POST",
            data: {count:count},
            success: function(html) {
                if(html.indexOf("finalize")==0){
                    var arrfn = html.split("_");
                    // console.log(arrfn);
                    $(".thank_you").css("display","block");
                    $(".last_your_order_code").html(arrfn[1]);
                    $(".main_box").css("display","none");
                    $(".main-slides").css("display","none");
                    $("#hidden_count").val(0);
                    off_finalize();

                }else if(html=='change_banner'){
                    setTimeout(function(){
                        $(".main_box").css("display","none");
                        $(".main-slides").css("display","block");
                        setTimeout(function(){
                            $(".thank_you").css("display","none");
                        },5000);
                        $("#hidden_count").val(count);
                    },5000);
                }else{
                    if(count>=cycle)
                        count = 0; 
                    else
                        count++;
                    $("#hidden_count").val(count);
                    $("#viewcart_box_right").html(html);
                    $(".main_box").css("display","block");
                    $(".main-slides").css("display","none");
                    $(".thank_you").css("display","none");
                }
            }
        });
    runload = setTimeout(function(){ load_customer_display() }, 2500);
}
function calc_height(){
    $(".main-slides").css("width",$( window ).width());
    $(".main-slides").css("height",$( window ).height());
}
function requestFullScreen(element) {      
      if(element.requestFullscreen) {
        element.requestFullscreen();
      } else if(element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
      } else if(element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen();
      } else if(element.msRequestFullscreen) {
        element.msRequestFullscreen();
      }
      $("#fullcreens").css("display:none");
}
function off_finalize(){
    $.ajax({
        url: "/carts/off-finalize",
        method: "POST",
        success: function(result){
        }
    });
}