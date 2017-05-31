<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="author" content="Anvy Developers">
        <title>BanhMiSub.com</title>

        <link href="http://fonts.googleapis.com/css?family=Roboto:400,900" rel="stylesheet" type="text/css" media="all">
        
        <link href="{{baseURL}}/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/bower_components/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css">

        <link href="{{baseURL}}/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">

        <link href="{{baseURL}}/themes/banhmisub/css/font.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/themes/banhmisub/css/main.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/themes/banhmisub/css/custom.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/themes/banhmisub/css/datetimepicker.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/themes/vendhq/js/jquery/lib/alert/css/jAlert-v3.css" rel="stylesheet" type="text/css">
        <link href="{{baseURL}}/themes/poscash/css/currentTableCss.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <div class="hidden">
            <img src="{{baseURL}}/themes/banhmisub/images/BmiSUB_logo.png" alt="logo" />
            <img src="{{baseURL}}/themes/banhmisub/images/BmiSUB_logo_border.png" alt="logo border" />
        </div>
        <div class="header shapdow-bot bartop" id="header">
            {{ partial('blocks/header') }}
        </div>
        <div id="main_menu_popup" class="modal fade" role="dialog">
            <div class="modal-dialog left_menu_popup">
                <div class="modal-content" style="background:#a03021;">
                    <div class="modal-body">
                        <div class="bootbox-body">
                            <div class="row">
                                <ul>
                                    <li><a href="{{ baseURL }}/orders/account_order">Payment On Account Order</a></li>
                                    <li><a href="{{ baseURL }}/orders/adjustment_order">Adjustment of Orders</a></li>
                                    <li><a href="{{ baseURL }}/orders/daily_history">View Daily Order History</a></li>
                                    <li><a href="{{ baseURL }}/online-station">Online Orders</a></li>
                                    <li><a href="#" onclick="openInternalTasks()">Internal tasks</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>         
        </div>
        <div id="menu_popup_center" class="modal fade" role="dialog">
            <div class="modal-dialog left_menu_popup">
                <div class="modal-content" style="background:#a03021;">
                    <div class="modal-body">
                        <div class="bootbox-body">
                            <div class="row">
                                <ul>
                                    <li>&nbsp;&nbsp;POS#: {{ session_user['session_order'] }}</li>
                                    <li><a href="{{ baseURL }}/user/logout">Sign out</a></li>
                                    {% if session_user['password'] is defined %}
                                    <li><a onClick="performShift('start', '{{session_user['_id']}}', '{{session_user['password']}}', '{{session_user['our_rep_id']}}', '{{session_user['our_rep']}}')" style="cursor:pointer;">Start Your Shift</a></li>
                                    <li><a onClick="performShift('end', '{{session_user['_id']}}', '{{session_user['password']}}', '{{session_user['our_rep_id']}}', '{{session_user['our_rep']}}')" style="cursor:pointer;">End your shift</a></li>
                                    {% endif %}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>         
        </div>        
        
        {% if title == 'Daily Order History' %}
            <div class="search_box customer_info" style="display:block">
                <form action="/orders/daily_history" id="myForm" method="POST">
                    <div class="form-inline" style="float:left;">
                      <div class="form-group">
                        <label class="sr-only" for="nameInfo">Code</label>
                        <input type="text" class="form-control" name="codeOrder" id="codeOrder" placeholder="Code" value="{{codeOrder}}" />
                      </div>
                      <input type="submit" class="btn btn-default botbg" style="color:white;" value="Find order" onclick="document.getElementById('myForm').submit();" />
                    </div>
                </form>
            </div>
        {% endif %}
        <div class="main-content">
            <!-- Static navbar -->
            {{ partial('blocks/nav') }}
            {{ content() }}
        </div>
        <!-- /container -->
        {{ partial('blocks/search_product')}}
        {% if session_user == false %}
            {{ partial('Users/signin')}}
            {{ partial('Users/createAccount')}}
        {% endif %}

        <div class="bgpopup" style="display:none"></div>
        <div class="popup_item popup-shapdow" style="display:none;">
            <div class="header_line barpopup">
                <div class="close_popup" onclick="closePopup();">x</div>
            </div>
            <div class="popup_content">
                <div class="productitem">
                    <div class="popup_title">
                        <h2>Banh mi SUB</h2>
                        <p class="description_item"></p>
                        <textarea class="note_product" style="display:none;"></textarea>
                        <button class="add_note_product" onclick="addNoteProduct()" style="display:block;"> Add Note</button>
                        <button class="hidden_note_product" onclick="hiddenNoteProduct()" style="display:none;"> Hidden Note</button>
                        <button class="clear_note_product" onclick="clearNoteProduct()" style="display:none;">Clear Note</button>
                    </div>
                    <div class="popup_image">
                        <img src="{{ baseURL }}/themes/banhmisub/images/default.png" alt="Banh mi" />
                    </div>
                </div>
                <div class="popup_prices">
                    <div class="op_description">
                    
                    </div>
                    <div class="popup_amount">
                        <button class="btn down mainbt" onclick="downQty('popup_qty_main')">-</button>
                        <input class="popup_qty" type="text" onfocus="onFocusQuantity(this)" value="1" id="popup_qty_main" />
                        <button class="btn up mainbt" onclick="upQty('popup_qty_main')">+</button>
                        <input id="sell_price_popup_qty_main" value="0" type="hidden" />
                    </div>
                    <div class="popup_price">
                        $10.00
                    </div>
                    <div class="popup_add_bt">
                        <button class="popup_add_cart totalbg" onclick="addCustomCart()" style="display:block">Add to Cart</button>
                        <button class="popup_update_cart totalbg" onclick="updateCart()" style="display:none">Update Cart</button>
                        <input class="item_id" type="hidden" value="" />
                    </div>
                    <div class="popup_control" style="display:none;">
                        <button class="back_level" onclick="optionLevel(1)" style="display:block;">«Back option</button>
                        <button class="next_level" onclick="optionLevel(2)" style="display:block;">Continues »</button>
                    </div>
                </div>
                <div class="tabs">
                    {{ partial('Categories/option_product') }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ partial('blocks/footer')}}
        </div>
        <div class="drapbox"></div>
        <!-- Loading -->
        <div class="loading" style="display:block;">
            <div class="loading-bg">
                <div class="logo-center" style="display:none;top: 372px;left: 650px;">
                    <div class="loading-logo" style="width: 310px;">
                    </div>
                </div>
            </div>
        </div>
        {% if !session_location %}
            {{ partial('blocks/location') }}
        {% endif %}

        <div class="alertbox alert_full" style="display:none;">
            <div class="alertbox_cont">
                Refunding money to customer : $25
            </div>
        </div>
        <div class="ring_box" style="display:none;">
            <input type="button" value="click" onclick="playmusic();" />
            <audio  id="play">
              <source src="/sounds/doorbell-1.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
            </audio>
        </div>
        <script type="text/javascript">
            var appHelper = {
                baseURL: '{{ baseURL }}',
				JT_URL: '{{ JT_URL }}'
            };
        </script>

        <script type="text/javascript" src="{{baseURL}}/bower_components/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/jquery-ui/jquery-ui.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/iscroll/build/iscroll.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/moment/min/moment.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/js/idle-timer.min.js"></script>

        <script type="text/javascript" src="{{baseURL}}/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="{{baseURL}}/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

        <script type="text/javascript" src="{{baseURL}}/themes/vendhq/js/jquery/lib/alert/js/jAlert-v3.js"></script>
        <script type="text/javascript" src="{{baseURL}}/themes/vendhq/js/jquery/lib/alert/js/jAlert-functions.js"></script>
        <script type="text/javascript" src="{{baseURL}}/themes/banhmisub/js/load.js"></script>
        <script type="text/javascript" src="{{baseURL}}/themes/banhmisub/js/function.js"></script>
        <script type="text/javascript" src="{{baseURL}}/themes/banhmisub/js/pricing.js"></script>
        <script type="text/javascript" src="{{baseURL}}/themes/banhmisub/js/common.js"></script>
    </body>
</html>