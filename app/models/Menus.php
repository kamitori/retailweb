<?php
namespace RW\Models;

class Menus extends ModelBase {

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var text
     */
    public $link;

    /**
     *
     * @var string
     */
    public $group_name;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $order_no;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'menus';
    }

    public function getParent()
    {
        $arrReturn = [];

        $menus = $this->find([
            'conditions' => 'parent_id = 0',
            'columns'    => ['id', 'name', 'group_name'],
            'order'      => 'order_no ASC'
        ]);

        $arrReturn = [];

        if ($menus) {
            foreach ($menus as $menu) {
                $arrReturn[$menu->group_name][] = ['value' => $menu->id, 'text' => $menu->name];
            }

            foreach ($arrReturn as $type => $menu) {
                array_unshift($arrReturn[$type], ['value' => 0, 'text' => '*Root']);
            }
        }

        return $arrReturn;
    }

    public function getTree()
    {
        $menus = $this->find([
            'columns'   => ['id', 'name', 'link', 'group_name', 'parent_id', 'order_no'],
            'order'     => 'order_no ASC'
        ]);

        $arrReturn = [];

        if ($menus) {
            foreach ($menus as $menu) {
                $arrReturn[$menu->group_name][] = $menu->toArray();
            }

            foreach ($arrReturn as $group => $menus) {
                $arrReturn[$group] = self::setMenu($menus);
            }

        }
        return $arrReturn;
    }

    private static function setMenu($menu)
    {
        $arrMenu = [];
        foreach($menu as $value){
            $arrMenu[$value['parent_id']][$value['id']]=$value;
        }
        return $arrMenu;
    }
}
