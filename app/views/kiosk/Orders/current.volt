<form>
    <div class="product_left shadow">
        <div class="box-table">
            <table cellpadding="0" cellspacing="0" class="sortable" id="current_order_list">
               <thead>
                  <tr>
                     <th class="no-sort">Orders</th>
                     <th data-sort='customer_po_no' data-sort-value="<?php if(isset($sort['customer_po_no'])){ echo $sort['customer_po_no']==1?'ASC':'DESC' ;} ?>">No</th>
                     <th data-sort='contact_name' data-sort-value="<?php if(isset($sort['contact_name'])){ echo $sort['contact_name']==1?'ASC':'DESC' ;} ?>">Contact</th>
                     <th data-sort='name' data-sort-value="<?php if(isset($sort['name'])){ echo $sort['name']==1?'ASC':'DESC' ;} ?>">Name</th>
                     <th class="no-sort" data-sort='quantity' data-sort-value="<?php if(isset($sort['quantity'])){ echo $sort['quantity']==1?'ASC':'DESC' ;} ?>">Quantity</th>
                     <th data-sort='salesorder_date' data-sort-value="<?php if(isset($sort['salesorder_date'])){ echo $sort['salesorder_date']==1?'ASC':'DESC' ;} ?>">Date</th>
                     <th class="no-sort" data-sort='price' data-sort-value="<?php if(isset($sort['price'])){ echo $sort['price']==1?'ASC':'DESC' ;} ?>">Price</th>
                  </tr>
               </thead>
               <tbody>
                    {% if(_list_orders) %}
                        {% for item in _list_orders %}
                            <tr>             
                                <td class="center">{{item['order']}}</td>
                                <td class="text">{{item['no']}}</td>
                                <td class="text">{{item['contact_name']}}</td>
                                <td class="text">{{item['name']}}</td>
                                <td class="center">{{item['quantity']}}</td>
                                <td class="center">{{item['dateCreated']}}</td>
                                <td class="price">{{item['price']}}</td>
                            </tr>          
                        {% endfor %}
                    {% else %}
                        <tr style="text-align:center;">
                            <td colspan="7">
                                No Records found
                            </td>
                        </tr>
                    {% endif %}
               </tbody>
            </table>                
        </div>
        <div class="_div_paginate">
            <div class="col-md-4 _paginage">
                {{_before}}
            </div>
            <div class="col-md-4 _paginage text_center">
                {{_current}}
            </div>
            <div class="col-md-4 text_right _paginage">
                {{_next}}
            </div>
        </div>
    </div>
    <div class="menu_right gray_bg" id="menu_scroll">
        <div class="right-menu-col">
            <div class="col-md-12 search_head">
                <div class="col-md-1">
                    <img style="max-height:25px;margin-top:5px;" src="/themes/poscash/images/Apps-Search-icon.png" />
                </div>
                <div class="col-md-10 search_title">
                    Search Order
                </div>            
                <div class="col-md-12 margin-top-15">            
                    <label for="_customer_name">
                        Customer:
                    </label>
                    <input placeholder="customer name" id="_customer_name" name="_customer_name" value="{{_customer_name}}" class="form-control" />
                </div>
                <div class="col-md-12 margin-top-15">            
                   <label for="_from">From:</label>
                   <div class="input-group date _datetimepicker">
                        <input placeholder="From time" name="_from" id="_from" value="{{_from}}" class="form-control _datetimepicker" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>                            
                    </div>                                       
                </div>
                <div class="col-md-12 margin-top-15">            
                    <label for="_to">To:</label>
                    <div class="input-group date _datetimepicker">
                        <input placeholder="To time" name="_to" id="_to" value="{{_to}}" class="form-control _datetimepicker" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>                            
                    </div> 
                </div>

                <div class="col-md-6 margin-top-30">
                    <input type="submit" value="Search" class="btn-block form-control btn"/>
                </div>
                <div class="col-md-6 margin-top-30">
                    <input type="reset" value="Clear" onclick="window.location.href='/poscash/orders/current'" class="btn-block form-control btn"/>
                </div>
            </div>
        </div>
    </div>
</form>