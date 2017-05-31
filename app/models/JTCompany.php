<?php
namespace RW\Models;
use \RW\Models\JTSalesaccount;
use \RW\Models\JTTax;

class JTCompany extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_company';
    }

    public static function getCompanyDefault(){
	    $arr_return = array('tax'=>5,'_id'=>'','taxkey'=>'','tax_no'=>'');
	    //id company Retail Customer
	    $company = self::findFirst([
	                        'conditions'  => [
	                            'deleted'       => false,
	                            'pos_default'    => 1,
	                        ],
	                        'sort'      => ['code' => -1]
	                    ]);
	   	if(!empty($company)){
	   		$arr_return['_id'] = (string)$company->_id;
	   		if(isset($company->account)){
	   			$arr_return['tax_no'] = (string)$company->account->tax_no;
	   		}
	   		$sales_account = JTSalesaccount::findFirst([
	                        'conditions'  => [
	                            'deleted'       => false,
	                            'company_id'    => $company->_id,
	                        ],
	                        'sort'      => ['code' => -1]
	                    ]);
	   		if(!empty($sales_account) && $sales_account->tax_code_id!=''){
	   			$tax = JTTax::findFirst([
	                        'conditions'  => [
	                            'deleted'       => false,
	                            'province_key'    => $sales_account->tax_code_id,
	                        ],
	                        'sort'      => ['code' => -1]
	                    ]);
	   			if($tax->fed_tax!=''){
	   				$arr_return['tax'] = $tax->fed_tax;
	   				$arr_return['taxkey'] = $tax->province_key;
	   			}
	   		}
	   	}

	   	return $arr_return;

	}
}

