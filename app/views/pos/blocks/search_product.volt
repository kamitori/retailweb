<!-- Modal -->
 <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title" id="myModalLabel">Start your order</h2>
            </div>
            <div class="modal-body">
                <div class="row border-bottom margin-bottom-20 padding-sides-64">
                    <div class="row padding-bottom-20">
                        <div class="col-md-6 text-right">
                            <label for="dispositionCarryout" class="modal-regular-text">
                            <span class="carryout-icon"></span>
                            Carryout
                            </label>&nbsp;
                            <input type="radio" name="disposition" id="dispositionCarryout" ng-value="dispositionOptions[0]" ng-model="search.disposition" class="ng-pristine ng-untouched ng-valid" value="">
                        </div>
                        <div class="col-md-6 text-left">
                            <label for="dispositionDelivery" class="modal-regular-text">
                            <span class="delivery-icon"></span>
                            Delivery
                            </label>&nbsp;
                            <input type="radio" name="disposition" id="dispositionDelivery" ng-value="dispositionOptions[1]" ng-model="search.disposition" class="ng-pristine ng-untouched ng-valid" value="">
                        </div>
                    </div>
                </div>
                <div id="DeliveryForm" class="row padding-sides-64 formpopup">
                    <form>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="modal-regular-text">Postal Code</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Enter Postal Code" />
                                    <small class="hidden">Postal Code is required.</small>
                                    <small class="hidden">Incorrect Postal Code format.</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <p class="ortext">or</p>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-2">
                                        <span class="map-icon"></span>
                                    </div>
                                    <div class="col-md-10 localtext">
                                        <a href="#" class="en-ca">
                                        <span class="white-caps">Your current location</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row margin-bottom-20">
                    <div class="col-md-5 fixwidth">
                        <hr class="hr-white-thin">
                    </div>
                    <div class="col-md-2 fixwidth">
                        <p class="ortext ortext2">or</p>
                    </div>
                    <div class="col-md-5 fixwidth">
                        <hr class="hr-white-thin">
                    </div>
                </div>
                <div id="AddressShip" class="row padding-sides-64 formpopup">
                    <form>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="modal-regular-text">Street Number</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Street Number" />
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-5">
                                <label class="modal-regular-text">Street Name</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Street Name" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="modal-regular-text">City</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="City" />
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-5">
                                <label class="modal-regular-text">Province</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Province" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="modal-regular-text">Apart., Hotel, Dorm Or Office?</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Apt/Unit/Suite Number" />
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-5">
                                <label class="modal-regular-text">Buzz Code, Building Name etc.</label>
                                <div class="control-group">
                                    <input type="text" name="postalcode" class="input-popup" placeholder="Buzz Code, Building Name etc." />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="button pizza-button popup-button">Find a store</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-5 fixwidth">
                        <hr class="hr-white-thin">
                    </div>
                    <div class="col-md-2 fixwidth">
                        <p class="ortext ortext2">or</p>
                    </div>
                    <div class="col-md-5 fixwidth">
                        <hr class="hr-white-thin">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 fixwidth">
                        <p class="text-center">Sign in to your account</p>
                    </div>
                </div>
                <div class="row formpopup">
                    <div class="col-md-12">
                        <form>
                            <div class="row">
                                <div class="col-md-7">
                                    <input type="text" name="postalcode" class="input-popup sign-input" placeholder="Email address" />
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="postalcode" class="input-popup sign-input" placeholder="Street Number" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 remembercheck">
                                    <input type="checkbox">&nbsp;Remember Me&nbsp;
                                    <a href="#">Forgot Password</a>
                                </div>
                                <div class="col-md-5">
                                    <button type="button" class="button pizza-button">Sign in</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>