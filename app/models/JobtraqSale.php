<?php
class JobtraqSale extends BaseModel {

    public static function count()
    {
    	if( AnvySetting::isActive('Estimate convertor Type') ){
    		$sale = new Order;
    	} else {
    		$sale = new Quotation;
    	}
    	return $sale::where('deleted', '=', false)
    				->where('customer_id', '=', new MongoId(User::userLogin()->_id))
    				->count();
    }

    public static function minimumTotal($data)
    {
        $minimum = User::userLogin()->minimum;
        if (isset($data['sum_sub_total'])) {
            if ($data['sum_sub_total'] < $minimum) {
                $data['sum_sub_total'] = $minimum;
                if (isset($data['taxval'])) {
                    $data['sum_amount'] = $data['sum_sub_total'] * ($data['taxval'] / 100);
                }
            }
        }
        return $data;
    }

    public static function minimumLine($total)
    {
        return [
                'sku'        => '',
                'name'       => 'Minimum Order Adjustment',
                'sell_price' => '',
                'sell_by'    => '',
                'sizew'      => '',
                'sizeh'      => '',
                'quantity'   => 1,
                'sub_total'  => $total,
                'note'       => '',
                'view_only'  => true
            ];
    }

	public static function getAddress(&$arrSave,$company)
	{
		if(!isset($arrSave['invoice_address'][0])){
			$arrSave['invoice_address'][0] = array(
		                                      'invoice_country'=>'Canada',
		                                      'invoice_country_id'=>'CA',
		                                      );
			$addresses_default_key = 0;
			if(isset($company['addresses_default_key']))
				$addresses_default_key = $company['addresses_default_key'];
			if(isset($company['addresses'][$addresses_default_key])){
				foreach($company['addresses'][$addresses_default_key] as $field => $value){
					if($field == 'name' || $field == 'deleted') continue;
					$arrSave['invoice_address'][0]['invoice_'.$field] = $value;
				}
			}
		}
		if(!isset($arrSave['shipping_address'][0]))
			$arrSave['shipping_address'][0] = array(
		                                      'shipping_country'=>'Canada',
		                                      'shipping_country_id'=>'CA',
		                                      );
		$arrSave['invoice_address'][0]['deleted'] = false;
		$arrSave['shipping_address'][0]['deleted'] = false;
	}

	public static function getCompanyInfo(&$arrSave,$company)
	{
        $arrSave['identity']      = new MongoId('5271dab4222aad6819000ed0');
		$arrSave['our_rep']       = isset($company['our_rep']) ? $company['our_rep'] : (isset($arrSave['our_rep']) ? $arrSave['our_rep'] : '');
		$arrSave['our_rep_id']    = isset($company['our_rep_id']) && strlen($company['our_rep_id']) == 24 ? new MongoId($company['our_rep_id']) : (isset($arrSave['our_rep_id']) ? $arrSave['our_rep_id'] : '');
		$arrSave['our_csr']       = isset($company['our_csr']) ? $company['our_csr'] : (isset($arrSave['our_csr']) ? $arrSave['our_csr'] : '');
		$arrSave['our_csr_id']    = isset($company['our_csr_id']) && strlen($company['our_csr_id']) == 24 ? new MongoId($company['our_csr_id']) : (isset($arrSave['our_csr_id']) ? $arrSave['our_csr_id'] : '');
		$arrSave['company_name']  = isset($company['name']) ? $company['name'] : '';
		$arrSave['company_id']    = isset($company['_id']) && strlen($company['_id']) == 24 ? new MongoId($company['_id']) : '';
        $contact = [];
        if( isset($company['contact_default_id']) && strlen($company['contact_default_id']) == 24 ) {
            $contact = Contact::select('_id','first_name', 'last_name')
                    ->where('_id', $company['contact_default_id'])
                    ->first();
            if( is_object($contact) )
                $contact = $contact->toArray();
            else
                $contact = [];
        }
        if( empty($contact) &&  is_object($arrSave['company_id']) ) {
            $contact = Contact::select('_id','first_name', 'last_name')
                    ->where('_id', $company['contact_default_id'])
                    ->where('company_id', $arrSave['company_id'])
                    ->orderBy('_id', 'asc')
                    ->first();
             if( is_object($contact) )
                $contact = $contact->toArray();
            else
                $contact = [];
        }
        if( !empty($contact) ) {
            $arrSave['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
            $arrSave['contact_id'] = new MongoId($contact['_id']);
        } else {
            $arrSave['contact_name'] = isset($company['contact_name']) ? $company['contact_name'] : '';
            $arrSave['contact_id'] = isset($company['contact_id']) && strlen($company['contact_id']) == 24 ? new MongoId($company['contact_id']) : '';
        }
		$arrSave['email'] = isset($company['email']) ? $company['email'] : '';
		$arrSave['phone'] = isset($company['phone']) ? $company['phone'] : '';
		if( isset($company['_id']) && is_object($company['_id']) ){
			$account = DB::collection('tb_salesaccount')
											->select('payment_terms', 'tax_code','tax_code_id')
											->where('company_id','=',new MongoId($company['_id']))
											->where('deleted', '=', false)
											->first();
		}
        $arrSave['payment_terms'] = isset($account['payment_terms']) ? $account['payment_terms'] : 0;
        if( isset($account['tax_code_id']) ){
        	$keytax = $account['tax_code_id'];
			$arr_tax = Tax::taxSelectList();
			$arrSave['tax'] = $keytax;
			$arrSave['taxval'] = (float)$arr_tax[$keytax];
        }

		self::getAddress($arrSave,$company);
	}

	public static function getTax(&$arrSave)
	{
		if( isset($arrSave['tax']) && isset($arrSave['taxval']) )
			return $arrSave;
		$taxper = 5;
		$arr_tax = Tax::taxSelectList();
		$key_tax = 'AB';
		if(isset($arrSave['invoice_address'][0]['invoice_province_state_id']) && $arrSave['invoice_address'][0]['invoice_province_state_id']!='')
			$key_tax = $arrSave['invoice_address'][0]['invoice_province_state_id'];
		if(isset($arr_tax[$key_tax])){
			$prov_key = explode("%",$arr_tax[$key_tax]);
			$taxper = $prov_key[0];
		}
		$arrSave['taxval'] = $taxper;
		$arrSave['tax'] = $key_tax;
	}

	public static function calSum($products)
	{
		$count_sub_total = 0;
        $count_amount = 0;
        $count_tax = 0;
        if(!empty($products)){
            foreach($products as $pro_key=>$pro_data){
                if(!$pro_data['deleted']){
                    if(isset($pro_data['option_for'])&&is_array($products[$pro_data['option_for']])
                        && (isset($products[$pro_data['option_for']]['sell_by'])&&$products[$pro_data['option_for']]['sell_by']=='combination'
                            || (isset($pro_data['same_parent'])&&$pro_data['same_parent']>0)
                            )
                        )
                        continue;
                    if(isset($pro_data['option_for'])
                       && isset($products[$pro_data['option_for']])
                       && ($products[$pro_data['option_for']]['deleted'] || isset($products[$pro_data['option_for']]['option_for']) )
                       )
                    	continue;
                    //cộng dồn sub_total
                    if(isset($pro_data['sub_total'])) {
                        $count_sub_total += round((float)$pro_data['sub_total'],2);
                    }
                    //cộng dồn amount
                    if(isset($pro_data['amount']))
                        $count_amount += round((float)$pro_data['amount'],2);
                }
            }
            //tính lại sum tax
            $count_tax = $count_amount - $count_sub_total;
        }
        return array('sum_amount'=>$count_amount,'sum_sub_total'=>$count_sub_total,'sum_tax'=>$count_tax);
	}

	public static function getDefault(&$arrData)
    {
    	$defaultField = self::returnField();
        foreach($defaultField as $field => $type){
            if( isset($arrData[$field]) ) continue;
            if( $type == 'string' ){
                $arrData[$field] = '';
            } else if( $type == 'number' ){
                $arrData[$field] = 0;
            } else if( $type == 'date' ){
                $arrData[$field] = new MongoDate();
            } else if( $type == 'bool' ){
                $arrData[$field] = false;
            } else if( $type == 'array' ){
                $arrData[$field] = [];
            } else if( is_array($type) && isset($type['default']) ) {
                $arrData[$field] = $type['default'];
            }
        }
        $arrData['created_by'] = new MongoId(User::userLogin()->_id);
        $arrData['modified_by'] = new MongoId(User::userLogin()->_id);
        $arrData['date_modified'] = new MongoDate();
        $arrData['customer_id'] = new MongoId(User::userLogin()->_id);
    }

    public static function createTmpSale($collection,$arrData)
    {
    	$arrData['deleted'] = true;
    	DB::connection(JT_DB)
    			->collection($collection)
    			->insert($arrData);
    	return $arrData['_id'];
    }
}