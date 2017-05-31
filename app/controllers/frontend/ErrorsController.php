<?php
namespace RW\Controllers;

class ErrorsController extends ControllerBase {

    private $data;

    public function initialize()
    {
        $this->data = $this->getData();
        parent::initialize();
    }

    public function notFoundAction()
    {
        $this->response->setStatusCode(404, $this->data['title']);
        $this->response->setContent($this->data['message']);
        $this->response->send();
    }

    public function uncaughtExceptionAction()
    {

    }

    private function getData()
    {
        return array_merge(['title' => '', 'message' => ''], $this->dispatcher->getParam('data'));
    }
}
