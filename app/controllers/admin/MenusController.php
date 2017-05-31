<?php
namespace RW\Controllers\Admin;

class MenusController extends ControllerBase {

    public function indexAction()
    {
        $arrData = [
            'parent' => $this->model->getParent()
        ];
        return $this->response(['error' => 0, 'data' => $arrData]);
    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['name' => '', 'link' => '', 'group_name' => '', 'parent_id' => 0, 'order_no' => 1], $data);
        if (isset($data['id']) && $data['id']) {
            $menu = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($menu) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $menu = new $this->model;
            $message = 'has been created';
        }
        $menu->name = $filter->sanitize($data['name'], 'string');
        $menu->link = $filter->sanitize($data['link'], 'string');
        $menu->group_name = $filter->sanitize($data['group_name'], 'string');
        $menu->parent_id = $filter->sanitize($data['parent_id'], 'int');
        $menu->order_no = $filter->sanitize($data['order_no'], 'int');
        if (isset($menu->id) && $menu->id === $menu->parent_id) {
            $menu->parent_id = 0;
        }
        if ($menu->save() === true) {
            $arrData = [
                'id'        => $menu->getId(),
                'parent'    => $this->model->getParent()
            ];
            $arrReturn = ['error' => 0, 'message' => 'Menu <b>'.$menu->name.'</b> '.$message.' successful.', 'data' => $arrData];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $menu->getMessages()];
        }

        return $this->response($arrReturn);
    }
}
