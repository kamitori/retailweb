<?php
class Setting extends BaseModel{

	protected $collection = 'tb_settings';

	public static function getSetting($setting_value, $sort = false)
	{
	 	$setting = Setting::select('option')
						->where('setting_value','=',$setting_value)
						->first();
		if( is_object($setting) )
			$setting = $setting->toArray();
		else
			return [];
		if(  isset($setting['option']) && !empty($setting['option']) ) {
			foreach($setting['option'] as $key => $value){
				if( isset($value['deleted']) && $value['deleted'] ) unset($setting['option'][$key]);
				if($setting_value == 'product_category' && (!isset($value['pubblic']) || $value['pubblic']!='1')){
					unset($setting['option'][$key]);
				}
			}
			if( $sort ) {
				usort($setting['option'], function($a, $b){
					return strcmp($a['name'], $b['name']);
				});
			}
			$setting = $setting['option'];
		}
		return $setting;
	}

	public static function getAllCountry()
	{
		$arrCountries = DB::collection('tb_country')
								->select('name', 'value')
								->where('deleted', false)
								->orderBy('name', 'asc')
								->remember(1800)
								->get();
		$arrReturn = [];
		if( !is_object($arrCountries) ) {
			foreach($arrCountries as $country){
				$arrReturn[$country['value']] = $country['name'];
			}
		}
		return $arrReturn;
	}

	public static function getAllProvinceByCountry($countryID = 'CA')
	{
		$arrProvinces = DB::collection('tb_province')
								->select('name', 'key')
								->where('deleted', false)
								->where('country_id', $countryID)
								->orderBy('name', 'asc')
								->remember(1800)
								->get();
		$arrReturn = [];
		if( !is_object($arrProvinces) ) {
			foreach($arrProvinces as $province){
				$arrReturn[$province['key']] = $province['name'];
			}
		}
		return $arrReturn;
	}

	public static function getDeliveryMethod()
	{
		$arrReturn = [];
		$arrMethods = self::where('setting_value', 'salesorder_delivery_method')
								->remember(1800)
								->pluck('option');
		if( !empty($arrMethods) ) {
			usort($arrMethods, function($a, $b){
				return strcmp($a['value'], $b['value']);
			});
			foreach($arrMethods as $method) {
				if( isset($method['deleted']) && $method['deleted'] ) continue;
				$arrReturn[$method['value']] = $method['name'];
			}
		}
		return $arrReturn;
	}
}