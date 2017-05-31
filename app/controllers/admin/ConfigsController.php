<?php
namespace RW\Controllers\Admin;

class ConfigsController extends ControllerBase {
    protected $notFoundMessage = 'This configure did not exist.';
    public function listAction()
    {
        $result = $this->listRecords(['id', 'cf_key', 'cf_value', 'status']);
        //pr($result);
        return $result;
    }
    public function editAction($id = 0)
    {
        $filter = new \Phalcon\Filter;
        $config = $this->model->findFirst($filter->sanitize($id, 'int'));
        if ($config) {
            return $this->response(['error' => 0, 'data' => $config->toArray()]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }
    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['cf_key' => '', 'cf_value' => '', 'status' => ''], $data);
        if (isset($data['id'])) {
            $config = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($config) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $config = new $this->model;
            $message = 'has been created';
        }

        $config->cf_key = $filter->sanitize($data['cf_key'], 'string');
        $config->cf_value = $data['cf_value'];
        $config->status = $filter->sanitize($data['status'], 'int');

        if ($config->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Configure <b>'.$config->cf_key.'</b> '.$message.' successful.', 'data' => ['id' => $config->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $config->getMessages()];
        }

        return $this->response($arrReturn);
    }

    public function getOptionGroupAction()
    {
        $arrReturn = ['error' => 0, 'data' => $this->model->getOptionGroup()];

        return $this->response($arrReturn);
    }

    public function getListUnitAction()
    {
        $data = $this->model->getListUnit();
        $arrReturn = ['error' => 0, 'data' => $data];

        return $this->response($arrReturn);
    }

}
