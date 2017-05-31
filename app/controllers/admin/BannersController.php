<?php
namespace RW\Controllers\Admin;

class BannersController extends ControllerBase {

    protected $notFoundMessage = 'This banner did not exist.';

    public function listAction()
    {
        return $this->listRecords(['id', 'image', 'link','position','order_no'], function($array) {
            if (isset($array['image'])) {
                if (!is_null($array['image'])) {
                    $array['image'] = URL.'/'.$array['image'];
                } else {
                    $array['image'] = '';
                }
            }
            $arr_pos = array('','Home left','Home right','Main backgound','Extra','Small Banner in Cart');
            if (isset($array['position']) && isset($arr_pos[$array['position']])){
                $array['position'] = $arr_pos[$array['position']];
            }
            return $array;
        });
    }

    public function editAction($id = 0)
    {
        return $this->editRecord($id, function($banner) {
            if (!is_null($banner->image)) {
                $banner->image = URL.'/'.$banner->image;
            }
            return $banner;
        });

    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['link' => ''], $data);
        if (isset($data['id'])) {
            $banner = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($banner) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $banner = new $this->model;
            $message = 'has been created';
        }
        $banner->link = $filter->sanitize($data['link'], 'string');
        $banner->order_no = $filter->sanitize($data['order_no'], 'string');
        $banner->position = $filter->sanitize($data['position'], 'string');
        if ($this->request->hasFiles() == true) {
            $imagePath = PUBLIC_PATH . DS . 'images' . DS . 'banners';
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            foreach($this->request->getUploadedFiles() as $file) {
                if (isImage($file->getType())) {
                    $fileName = $file->getName();
                    $fileExt = $file->getExtension();

                    $fileName = str_replace('.'.$fileExt, '_'.date('d-m-y').'.'.$fileExt, \Phalcon\Text::uncamelize($fileName));

                    if ($file->moveTo($imagePath . DS . $fileName)) {
                        if (isset($banner->image) && file_exists(PUBLIC_PATH . DS . $banner->image)) {
                            unlink(PUBLIC_PATH . DS . $banner->image);
                        }
                        $banner->image = 'images/banners/'.$fileName;
                    }
                    break;
                }
            }
        }
        if ($banner->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Banner <b>'.$banner->name.'</b> '.$message.' successful.', 'data' => ['id' => $banner->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $banner->getMessages()];
        }

        return $this->response($arrReturn);
    }
}
