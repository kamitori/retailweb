<div class="modal fade" id="modal_location">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Find a store</h4>
			</div>
			<div class="modal-body row">
				<div class="form-group col-xs-6">
                    <label class="control-label col-xs-4 text-right">Postal code</label>
                    <div class="col-xs-8">
                        <input type="text" id="postal_code_location" class="form-control" placeholder="Postal code">
                    </div>
                </div>
                <div class="col-xs-12">
                	<div class="col-xs-5">
						<hr/>
					</div>
					<div class="col-xs-2 text-center">
						<b>Or</b>
					</div>
					<div class="col-xs-5">
						<hr/>
					</div>
                </div>
                
                <div class="form-group col-xs-6">
                    <label class="control-label col-xs-4 text-right">Street number</label>
                    <div class="col-xs-8">
                        <input type="text" id="street_number_location" class="form-control" placeholder="Street number">
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label class="control-label col-xs-4 text-right">Street name</label>
                    <div class="col-xs-8">
                        <input type="text" id="street_name_location" class="form-control" placeholder="Street name">
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label class="control-label col-xs-4 text-right">City</label>
                    <div class="col-xs-8">
                        <input type="text" id="city_location" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label class="control-label col-xs-4 text-right">Province</label>
                    <div class="col-xs-8">
                        <select id="province_location" class="form-control">
							<option value="AB">Alberta</option>
							<option value="BC">British Columbia</option>
							<option value="MB">Manitoba</option>
							<option value="NB">New Brunswick</option>
							<option value="NL">Newfoundland and Labrador</option>
							<option value="NS">Nova Scotia</option>
							<option value="ON">Ontario</option>
							<option value="PE">Prince Edward Island</option>
							<option value="QC">Quebec</option>
							<option value="SK">Saskatchewan</option>
							<option value="NT">Northwest Territories</option>
							<option value="NU">Nunavut</option>
							<option value="YT">Yukon</option>
						</select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                	<div class="col-xs-4 col-xs-offset-4">
                		<button class="btn btn-block" onclick="find_location()">Find store</button>
                	</div>
                </div>
                <div class="form-group col-xs-12" id="find_location_result">
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<style>
	#modal_location .modal-dialog{
		width:860px;
	}
</style>