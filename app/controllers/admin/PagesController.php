<?php
namespace RW\Controllers\Admin;

class PagesController extends ControllerBase {

    protected $notFoundMessage = 'This page did not exist.';

    public function listAction()
    {
        return $this->listRecords(['id', 'name', 'order_no']);
    }

    public function editAction($id = 0)
    {
        $filter = new \Phalcon\Filter;
        $pages = $this->model->findFirst($filter->sanitize($id, 'int'));
        if ($pages) {
            if (!is_null($pages->image)) {
                $pages->image = URL.'/'.$pages->image;
            }
            $pages = $pages->toArray();
            $category = new \RW\Models\Categories;
            $pages['categoryOptions'] = $category->getOptions();
            return $this->response(['error' => 0, 'data' => $pages]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['name' => '', 'content' => '', 'order_no' => 1, 'meta_title' => '', 'meta_description' => ''], $data);
        if (isset($data['id'])) {
            $page = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($page) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $page = new $this->model;
            $message = 'has been created';
        }
        $page->name = $filter->sanitize($data['name'], 'string');
        $page->summary = $data['summary'];
        $page->content = $data['content'];
        $page->category_id = $filter->sanitize($data['category_id'], 'int');
        $page->order_no = $filter->sanitize($data['order_no'], 'int');
        $page->meta_title = $filter->sanitize($data['meta_title'], 'string');
        $page->meta_description = $filter->sanitize($data['meta_description'], 'string');
        if ($this->request->hasFiles() == true) {
            $imagePath = PUBLIC_PATH . DS . 'images' . DS . 'pages';
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            foreach($this->request->getUploadedFiles() as $file) {
                if (isImage($file->getType())) {
                    $fileName = $file->getName();
                    $fileExt = $file->getExtension();

                    $fileName = str_replace('.'.$fileExt, '_'.time().'.'.$fileExt, \Phalcon\Text::uncamelize($fileName));

                    if ($file->moveTo($imagePath . DS . $fileName)) {
                        if (isset($page->image) && file_exists(PUBLIC_PATH . DS . $page->image)) {
                            unlink(PUBLIC_PATH . DS . $page->image);
                        }
                        $page->image = 'images/pages/'.$fileName;
                    }
                    break;
                }
            }
        }
        if ($page->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Page <b>'.$page->name.'</b> '.$message.' successful.', 'data' => ['id' => $page->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $page->getMessages()];
        }

        return $this->response($arrReturn);
    }
}
