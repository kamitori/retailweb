<?php
namespace RW\Controllers\Admin;

class CategoriesController extends ControllerBase {

    protected $notFoundMessage = 'This category did not exist.';

    public function listAction()
    {
        return $this->response(['error' => 0, 'data' => (new \RW\Models\JTSettings)->type('product_category')->get()]);
    }

    public function editAction($id = 0)
    {
        $filter = new \Phalcon\Filter;
        $categories =  (new \RW\Models\JTSettings)->type('product_category')->get();
        $id = $filter->sanitize($id, 'int');
        $found = false;
        $arrCategories = [];
        foreach ($categories as $key => $cate) {
            if ($cate['id'] == $id) {
                $found = true;
                $category = $cate;
                continue;
            }
            $arrCategories[] = ['value' => $cate['id'], 'text' => $cate['name']];
        }
        if ($found) {
            if (isset($category['image']) && !is_null($category['image'])) {
                $category['image'] = URL.'/'.$category['image'];
            }
            $category['categories'] = $arrCategories;
            return $this->response(['error' => 0, 'data' => $category]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['name' => '', 'description' => '', 'meta_title' => '', 'meta_description' => ''], $data);
        $categories =  (new \RW\Models\JTSettings)->type('product_category')->get(true);
        if (isset($data['id'])) {
            $id = $filter->sanitize($data['id'], 'int');
            $category = isset($categories[$id]) ? $categories[$id] : [];
            if ($category) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $category = [];
            $message = 'has been created';
        }
        $category['deleted'] = false;
        $category['name'] = $filter->sanitize($data['name'], 'string');
        $category['value'] = $filter->sanitize($data['name'], 'string');
        $category['description'] = $filter->sanitize($data['description'], 'string');
        $category['meta_title'] = $filter->sanitize($data['meta_title'], 'string');
        $category['meta_description'] = $filter->sanitize($data['meta_description'], 'string');
        $category['parent_id'] = $filter->sanitize($data['parent_id'], 'string');
        $category['position'] = $filter->sanitize($data['position'], 'string');
        $category['order_no'] = $filter->sanitize($data['order_no'], 'string');

        if ($this->request->hasFiles() == true) {
            $imagePath = PUBLIC_PATH . DS . 'images' . DS . 'product-categories';
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            foreach($this->request->getUploadedFiles() as $file) {
                if (isImage($file->getType())) {
                    $fileName = $file->getName();
                    $fileExt = $file->getExtension();

                    $fileName = str_replace('.'.$fileExt, '_'.date('d-m-y').'.'.$fileExt, \Phalcon\Text::uncamelize($fileName));

                    if ($file->moveTo($imagePath . DS . $fileName)) {
                        if (isset($category['image']) && file_exists(PUBLIC_PATH . DS . $category['image'])) {
                            unlink(PUBLIC_PATH . DS . $category['image']);
                        }
                        $category['image'] = 'images/product-categories/'.$fileName;
                    }
                    // mkdir($imagePath . DS . $fileName, 0755, true);
                    break;
                }
            }
        }
        if (isset($id)) {
            $categories[$id] = $category;
        } else {
            $categories[] = $category;
            $id = count($categories) - 1;
        }
        $name = $category['name'];
        $category = $this->model->findFirst([$this->model->conditions]);
        $category->option = $categories;
        if ($category->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Category <b>'.$name.'</b> '.$message.' successful.', 'data' => ['id' => $id]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $category->getMessages()];
        }

        return $this->response($arrReturn);
    }

    public function deleteAction($id)
    {
        $filter = new \Phalcon\Filter;
        $arrReturn = ['error' => 1];

        $categories =  $this->model->get(true);
        $id = $filter->sanitize($id, 'int');
        $category = isset($categories[$id]) ? $categories[$id] : [];

        if ($category) {
            $category['deleted'] = true;
            $categories[$id] = $category;
            $category = $this->model->findFirst([$this->model->conditions]);
            $category->option = $categories;
            $category->save();
            $arrReturn = ['error' => 0, 'message' => 'This record was deleted successful.'];
        } else {
            $arrReturn['message'] = 'This record did not exist.';
        }

        return $this->response($arrReturn);
    }

    public function getOptionsAction()
    {
        $arrReturn = ['error' => 0, 'data' => (new \RW\Models\JTSettings)->type('product_category')->getSelect()];
        return $this->response($arrReturn);
    }

}
