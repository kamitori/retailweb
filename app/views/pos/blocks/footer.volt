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
                <iframe src="{{baseURLPos}}/print-order" id="priting_iframe"></iframe>
                <button type="button" class="btn btn-default totalbg refund_money" id="finalize">
                    Refund and finish
                </button>
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

<div id="footer" class="botbg">
    <div class="block_footer lable">
        <button class="full_screen_bt" onclick="launchIntoFullscreen(document.documentElement);$(this).hide();"> </button>
        Sub Cart:
    </div>
    <div class="block_footer" id="number_in_cart">
        <span class="cart-total">{{ cart['quantity'] }}</span> Items in Cart
    </div>
    <div class="block_footer">
        <i class="fa fa-shopping-cart"></i> <a href="#" id="view_cart" rel="off"> View cart </a> <i class="fa fa-caret-up"></i>
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
        <div class="form-inline" style="float:left;margin:5px 0px 0px 5px;color: white;width: 60%;">
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
        <div class="form-group pm_cash_type repaybox">
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

    </div>
</div>


