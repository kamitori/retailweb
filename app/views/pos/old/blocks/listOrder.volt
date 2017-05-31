<div id="select-sale" class="padded-20 contentShow" style="display: none;">
   <div class="section">
      <form class="ng-pristine ng-valid">
         <div class="subheading">
            <h1 class="strong" style="padding-left:3%;margin-top:1%">Select a Sale to Open</h1>
         </div>
         <div id="sales_list_container">
            <div class="table-wrapper">
               <table class="item_list table-padded" id="register-sale-list">
                  <thead>
                     <tr>
                        <th>Date/time</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Customer</th>
                        <th>Code</th>
                        <th>Total</th>
                        <th>Note</th>
                        <th></th>
                     </tr>
                  </thead>
                  <tbody>                     
                     {% for item in orderList %}
                         <tr class="selectable" id="oL-{{item['id']}}">
                           <td>{{item['created_at']}}</td>
                           <td>SAVED</td>
                           <td>{{item['userName']}}</td>                           
                           <td>{{item['customerName']}}</td>
                           <td>{{item['code']}}</td>
                           <td id="op-{{item['id']}}">${{item['totalPrice'] + item['totalTax']}}</td>
                           <td>{{item['description']}}</td>
                           <td>
                              <div class="controls">
                                 <ul>
                                    <li><a onclick="clickToOpen('{{item['id']}}')" href="javascript:void(0);">Open</a></li>
                                 </ul>
                              </div>
                           </td>
                        </tr> 
                     {% endfor %}
                     {% if(!orderList) %}
                        <tr class="selectable" style="text-align:right">
                        <td style="text-align: center;" colspan="8">
                           No saved sales
                        </td>
                     </tr>
                     {% endif %}
                     <tr id="retrieve_more_sales_row" class="selectable" style="text-align:right;display:none">
                        <td style="text-align: center;" colspan="8"><a href="#">Show More</a></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </form>
   </div>
</div>