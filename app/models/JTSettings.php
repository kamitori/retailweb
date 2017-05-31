<?php
namespace RW\Models;

class JTSettings extends MongoBase {

    public $type = null;

    public function initialize()
    {
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tb_settings';
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function get($includeDeleted = false)
    {
        if (is_null($this->type)) {
            throw new \Exception('Type must be defined.');
        }
        $results = $this->findFirst([[
                            'setting_value' => $this->type
                        ]])->toArray();
        if (isset($results['option'])) {
            foreach ($results['option'] as $key => $result) {
                if (!$includeDeleted && (isset($result['deleted']) && $result['deleted'] || !isset($result['deleted']))) {
                    unset($results['option'][$key]);
                    continue;
                }
                $results['option'][$key]['id'] = $key;
            }
            usort($results['option'], function($a, $b) {
                return $a['name'] > $b['name'];
            });
            return array_values($results['option']);
        }
        return [];
    }

    public function getSelect()
    {
        $arrReturn = [];
        $results = $this->get();
        foreach ($results as $key => $result) {
            $arrReturn[] = ['value' => $result['value'], 'text' => $result['name']];
        }
        return $arrReturn;
    }

    public function getFinishOption($arr_pro=array())
    {
        $arr_group = $arrReturn = [];
        $results = $this->findFirst([[
                            'setting_value' => 'product_option_type'
                        ]]);
        if(isset($results->option)) {
            foreach ($results->option as $key => $result) {
                $arr_group[] = $result['value'];
            }
        }
        $groupsetting = $this->find(array(
                    'conditions'=>array('setting_value'=>array('$in'=>$arr_group))
        ));
        foreach ($groupsetting as $setting){
            foreach ($setting->option as $key => $value) {
                $arr_setting[$setting->setting_value][$value['value']] = $value['name'];
            }
        }
        foreach ($arr_pro as $key => $value) {
            if(isset($arr_setting[$value]))
                $arrReturn[$key] = $arr_setting[$value];
        }
        return $arrReturn;
    }
}
