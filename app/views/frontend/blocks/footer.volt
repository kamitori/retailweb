<div class="modal fade" id="cart-note-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order note</h4>
            </div>
            <div class="modal-body">
                <textarea id="cart-note" onchange="updateCartNote(this)" class="form-control" style="resize: none;" rows="15">{{ cart['note'] }}</textarea>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="print-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order note</h4>
            </div>
            <div class="modal-body printing_box">
                <iframe src="{{baseURL}}/print-order" id="priting_iframe"></iframe>
                <!-- <button type="button" class="btn btn-default totalbg refund_money" id="finalize">
                    Refund and finish
                </button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="payment-calculation-modal" role="dialog">
    <div class="modal-dialog" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Payment Part</h4>
            </div>
            <div class="modal-body payment_calculation_box">

            </div>
            <input type="hidden" id="payment_for_order" value="" />
            <input type="hidden" id="payment_completed" value="" />
        </div>
    </div>
</div>
<div class="modal fade" id="use-group-modal" role="dialog">
    <div class="modal-dialog" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Please enter the name of Guest</h4>
            </div>
            <div class="modal-body use_group_box">
                <p>Please input name  of member in group !</p>
                <form class="use_group_form">
                    <div class="form-group">
                        <label for="username_1"></label>
                        <input type="text" class="form-control username" id="username_1" placeholder="Name of Guest 1" value="Name of Guest {% if next_uid is defined %}{{next_uid}}{%endif%}">
                    </div>
                </form>
                <!-- <button type="button" class="btn btn-default" style="float:right;" onclick="AddLineUsename()">Add more Member</button> -->
                <button type="button" class="btn btn-default" id="begin_choice_product" data-proid="" onclick="setGroup();">Select Menu Item</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="order_detail" role="dialog">
    <div class="modal-dialog" style="width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Order detail</h4>
            </div>
            <div class="modal-body order_detail_box" style="padding:0;">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="choice-customer" role="dialog">
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Select Customer</h4>
                <button onclick="finalize(1)" class="btn btn-bg-black choice-customer-bt">Print order</button>
            </div>
            <div class="modal-body">
                Please enter customer name or select from existing list:<br /><br />
                <form id="customer_info">
                    <div class="row">
                        <div class="col-xs-5">
                            <label for="cs_fullname">Fullname</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cs_fullname" placeholder="Fullname">
                                <input type="hidden" value="" class="form-control" id="cs_contact_id">
                                <div class="input-group-addon" style="cursor:pointer;" onclick="popup_list('contact')">Select</div>
                            </div>
                        </div>
                        <div class="col-xs-2"></div>
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="cs_phone">Phone</label>
                                <input type="text" class="form-control" id="cs_phone" placeholder="No Phone">
                            </div>
                        </div>
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="cs_email">Email</label>
                                <input type="email" class="form-control" id="cs_email" placeholder="No Email">
                            </div>
                        </div>
                        <div class="col-xs-2"></div>
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="cs_company">Company</label>
                                <input type="text" class="form-control" id="cs_company" placeholder="No Company name">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="cs_address_1">Address 1</label>
                                <input type="text" class="form-control" id="cs_address_1" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="cs_address_2">Address 2</label>
                                <input type="email" class="form-control" id="cs_address_2" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="cs_address_3">Address 3</label>
                                <input type="email" class="form-control" id="cs_address_3" placeholder="">
                            </div>
                        </div>
                        <div class="col-xs-2"></div>
                        <div class="col-xs-5">
                            <div class="form-group">
                                <label for="cs_town_city">Town / City</label>
                                <input type="text" class="form-control" id="cs_town_city" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="cs_province_state">Province / State</label>
                                <input type="text" class="form-control" id="cs_province_state" placeholder="">
                                <input type="hidden" class="form-control" id="cs_province_state_id" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="cs_zip_postcode">Zip / Post code</label>
                                <input type="text" class="form-control" id="cs_zip_postcode" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="cs_country">Country</label>
                                <input type="text" class="form-control" value="Canada" id="cs_country" placeholder="">
                                <input type="hidden" class="form-control" value="CA" id="cs_country_id" placeholder="">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="select-contact" role="dialog">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Select contact</h4>
            </div>
            <div class="modal-body contact_list_box" style="padding:0;">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cart-list-free" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Free list items</h4>
            </div>
            <div class="modal-body">
                Please choice <span id="free_total_span">0</span> items for free :
                <div class="free_items_list row" style="width: 94%;margin: 3%;display: table;">    
                    <div class="free_item col-xs-12">
                        <div class="cartbox_des">
                            <div class="cartbox_img">
                                <img src="http://jt.banhmisub.com/upload/2016_04/2016_04_21_164922_254362.png" alt="Water Bottle" onclick="" style="cursor:pointer;">
                            </div>
                            <div class="cartbox_name">
                                <h3>Water Bottle</h3>
                                <div class="sell_price">
                                    Unit price: <b>$1.65</b>
                                </div>
                                <div class="cart_note"></div>
                                <div class="description_item" style="display:none">Nước Suối</div>
                            </div>
                        </div>
                        <div class="cartbox_qty">
                            <div class="qtybox">
                                <button class="btn down mainbt" onclick="calFreeQty('down','5716ff49124dcada0530dc80')">-</button>
                                <input class="free_qty" type="text" value="0" id="free_item_5716ff49124dcada0530dc80" style="width: 55px;">
                                <button class="btn up mainbt" onclick="calFreeQty('up','5716ff49124dcada0530dc80')">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="cartbox_item col-xs-12">
                        <div class="cartbox_des">
                            <div class="cartbox_img">
                                <img src="http://jt.banhmisub.com/upload/2016_04/2016_04_21_161955_551925.png" alt="POP Drink" style="cursor:pointer;">
                            </div>
                            <div class="cartbox_name">
                                <h3>POP Drink</h3>
                                <div class="sell_price">
                                    Unit price: <b>$1.65</b>
                                </div>
                                <div class="cart_note"></div>
                                <div class="description_item" style="display:none"></div>
                            </div>
                        </div>
                        <div class="cartbox_qty">
                            <div class="qtybox">
                                <button class="btn down mainbt" onclick="calFreeQty('down','5681c266124dcae13ab42c3e')">-</button>
                                <input class="free_qty" type="text" value="0" id="free_item_5681c266124dcae13ab42c3e" style="width: 55px;">
                                <button class="btn up mainbt" onclick="calFreeQty('up','5681c266124dcae13ab42c3e')">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ partial('blocks/task') }}

<div id="footer" class="botbg">
    <div class="block_footer lable">
        <button class="full_screen_bt" onclick="launchIntoFullscreen(document.documentElement);$(this).hide();"> </button>
        Sub Cart:
    </div>
    <div class="block_footer" id="number_in_cart">
        <span class="cart-total">{{ cart['quantity'] }}</span> Items in Cart
    </div>
    <div class="block_footer">
        <i class="fa fa-shopping-cart"></i> <a href="#" id="view_cart" rel="off"> View cart </a> <i class="fa fa-caret-up" id="view_cart_downup"></i>
    </div>
    <div class="block_footer">
        <button type="button" class="btn btn-default clear_cart_bt" style="display:inline-block;margin-top: -8px;">Clear Cart</button>
    </div>
    <div class="block_footer totalbg totalbox" id="total">
        <div class="rerotate">Total: <span class="main_total">$<?php echo number_format((double)($cart['main_total']),2) ?></span></div>
    </div>
    <div class="block_footer botbg totalbox" id="checkout">
        <div class="resetrote rerotate">Place order <!-- <i class="fa fa-caret-right"> </i>--></div>
    </div>
</div>
<div id="footer_cart" class="smallbox" style="height:0px;">
    {{ partial('blocks/small_cart') }}
</div>

<div id="footer_cart_detail" style="height:0px;">
    <div class="customer_info" style="display:none;">
        <div class="conform_order">
            Please review the order before saving
        </div>
        <div class="form-inline" style="float:left;">
          <div class="form-group">
            <label class="sr-only" for="emailInfo">Customer Email</label>
            <input type="email" class="form-control" id="emailInfo" placeholder="Customer email" onchange="findEmail(this);">
            <div id="findResult" style="display:none;">

            </div>
          </div>
          <div class="form-group">
            <label class="sr-only" for="nameInfo">Customer Name</label>
            <input type="text" class="form-control" id="nameInfo" placeholder="Customer name">
          </div>
          <button type="submit" class="btn btn-default botbg" style="color:white;" id="btnAddContact" onclick="createContact()">Add customer</button>
        </div>
    </div>
    <div id="cartbox" class="row col-xs-12">
        <div class="cartbox row">
            {{ partial('blocks/view_cart') }}
        </div>
    </div>
    <div class="cash_info botbg" style="display:none">
        <div class="form-inline" style="float:left;margin:5px 0px 0px 5px;color: white;width: 60%;display:none">
            <div class="form-group black_bt">
                <button type="button" class="btn btn-default totalbg order_list_bt" style="display:inline-block;">
                    Draft
                </button>
                <button type="button" class="btn btn-default totalbg back_to_cart" style="display:none;">
                    Current
                </button>
                <button type="button" class="btn btn-default totalbg" data-toggle="modal" data-target="#cart-note-modal">
                    Note
                </button>
                <label for="Paidby" style="margin-left: 15px;">Paid by</label>
                <select class="form-control payment_type" id="Paidby" onchange="changePaymentType();">
                    <option value="Cash">Cash</option>
                    <option value="On Account">On Account</option>
                    <option value="Credit card">Credit card</option>
                </select>
            </div>
            <div class="form-group pm_cash_type paymentlabel">
                <label for="cash_tend">Payment: $</label>
                <input type="text" class="form-control" id="cash_tend" value="0.00" onclick="onFocusQuantity(this,'cash_tend')" />
            </div>

        </div>
        <div class="form-group pm_cash_type repaybox" style="display:none">
            <div class="form-group">
                <label>Repay: $</label>
                <div class="change_due">0.00</div>
            </div>
        </div>
        <div id="cartbox_action" class="row col-xs-12">
            <button type="button" class="btn btn-default totalbg totalbox finalizes" data-toggle="modal" data-target="#print-modal">
                Finalize
            </button>
         </div>
    </div>
    <div class="cart_tool botbg" style="display:none">
        <button type="button" class="btn btn-default totalbg order_list_bt" style="display:inline-block;">
            Draft list
        </button>
        <button type="button" class="btn btn-default totalbg back_to_cart" style="display:none;">
            Current list
        </button>
        <button type="button" class="btn btn-default totalbg" data-toggle="modal" data-target="#cart-note-modal">
            Edit Note
        </button>
        <button type="button" class="btn btn-default totalbg saveorder" id="saveorder">
            Save order
        </button>
        <button type="button" class="btn btn-default totalbg" id="exit_from_cart">
            Back
        </button>
    </div>
</div>


