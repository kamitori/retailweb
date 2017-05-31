<?php
namespace RW\Models;

class Categories extends MongoBase {


    public $conditions = [
                'setting_value' => 'product_category'
            ];

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

    public function get($includeDeleted = false)
    {
        $categories = $this->findFirst([
                $this->conditions
            ])->toArray();
        if (isset($categories['option'])) {
            foreach ($categories['option'] as $key => $category) {
                if (!$includeDeleted && isset($category['deleted']) && $category['deleted']) {
                    unset($categories['option'][$key]);
                    continue;
                }
                $categories['option'][$key]['id'] = $key;
            }
            usort($categories['option'], function($a, $b) {
                return $a['name'] > $b['name'];
            });
            return array_values($categories['option']);
        }
        return [];
    }

    public static function getOptions()
    {
        self::get();
    }
}
