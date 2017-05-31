<div id="close-register" class="contentShow" style="display:none">
   <div class="edit_details">
      <div class="section">
         <div class="printable padded-20">
            <div class="subheading">
               <h1 class="strong">Closing totals to verify</h1>
            </div>
            <h2>Register details</h2>
            <div class="hr-top top-bottom-gap">
               <div class="section line attributes">
                  <div class="size1of2 unit">
                     <div class="attribute">
                        <label>Register:</label>
                        <span>Main Register</span>
                     </div>
                     <div class="attribute">
                        <label>Outlet:</label>
                        <span>Main Outlet</span>
                     </div>
                  </div>
                  <div class="size1of2 unit">
                     <div class="attribute">
                        <label>Opened:</label>
                        <span>October 16, 2015 4:28 AM</span>
                     </div>
                     <div class="attribute">
                        <label>Closed:</label>
                        <span>October 16, 2015 4:47 AM</span>
                     </div>
                  </div>
               </div>
            </div>
            <br>
            <h2>Sales</h2>
            <div class="section">
               <div class="table-wrapper">
                  <table class="accounts" id="sales_summary">
                     <tbody>
                        <tr class="total">
                           <th colspan="2">New sales</th>
                           <th></th>
                           <th colspan="2" class="currency">{{_closeRegister['_subTotal']}}</th>
                        </tr>
                        <tr>
                           <td></td>
                           <td colspan="2">New</td>
                           <td colspan="2" class="currency">{{_closeRegister['_subTotal']}}</td>
                        </tr>
                        <tr>
                           <td></td>
                           <td colspan="2">
                              Tax                   
                           </td>
                           <td colspan="2" class="currency">
                              {{_closeRegister['_subTax']}}
                           </td>
                        </tr>
                        <tr class="total">
                           <th>Discounts</th>
                           <th colspan="2"></th>
                           <th colspan="2" class="currency">
                              {{_closeRegister['_subDiscount']}}
                           </th>
                        </tr>
                        <tr class="total">
                           <th>Payments</th>
                           <th colspan="2"></th>
                           <th colspan="2" class="currency">{{_closeRegister['_subTotal']}}</th>
                        </tr>
                        <tr>
                           <td></td>
                           <td colspan="2">New</td>
                           <td colspan="2" class="currency">{{_closeRegister['_subTotal']}}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <form id="close-register-form" action="/register/31eb0866-e756-11e5-fed9-671207f960f1/close?ajax=1" method="post">
               <br>
               <h2>Payments</h2>
               <div class="section">                      
                  <fieldset>
                     <div class="table-wrapper">
                        <table class="item_list">
                           <thead>
                              <tr>
                                 <th>Payment</th>
                                 <th class="currency">Amount</th>
                                 <th>To post</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td>
                                    Cash                            
                                 </td>
                                 <td class="currency">
                                    {{_closeRegister['_subTotal']}}
                                 </td>
                                 <td>
                                    <div class="form-row">
                                       <label > </label>
                                       <div class="form-field">
                                          <input disabled type="text" value="{{_closeRegister['_subTotal']}}" class="number currency noprint" >
                                          <span class="print"> {{_closeRegister['_subTotal']}}</span>
                                          <div class="error"></div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>
                                    Credit Card                            
                                 </td>
                                 <td class="currency">
                                    0.00                            
                                 </td>
                                 <td>
                                    <div class="form-row">
                                       <label for=""> </label>
                                       <div class="form-field">
                                          <input value="$0" type="text" disabled class="number currency noprint" id="">
                                          <span class="print"> {{_closeRegister['_subTotal']}}</span>
                                          <div class="error"></div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </fieldset>
               </div>
               <div class="clearer"></div>
               <br>
               <div class="form-button-bar noprint" style="clear: both;min-height:40px;">
                  <button value="save" type="button" onclick="CloseRegister()" class="btn btn--primary button">Close Register</button>
                  <a href="javascript:void(0)" onclick="printCloseRegister()" id="btn-close-print" class="btn">Print</a>
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="clearer"></div>
</div>