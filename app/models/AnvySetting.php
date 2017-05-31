<?php
class AnvySetting extends BaseModel {

	protected $collection = 'anvy_setting';

	protected $defaultField = [
				'deleted' 	=> 	'bool',
				'active'	=> 	['default' => 1],
				'description'	=> 'string',
				'configures'	=> 'array'
	];

	protected $rules = [
			'name' => 'required|min:5',
			'active' => 'integer',
		];

	public static function getSetting($name)
	{
		$arrReturn = [];
		$configures = self::where('deleted', false)
				->where('active', 1)
				->where('name', $name)
				->pluck('configures');
		if( !empty($configures) ) {
			foreach($configures as $configure) {
				$arrReturn[ $configure['key'] ] = $configure['value'];
			}
		}
		return $arrReturn;
	}

	public static function isActive($name)
	{
		return self::where('deleted', false)
				->where('active', 1)
				->where('name', $name)
				->count();
	}
}