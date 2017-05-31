<?php
namespace RW\Controllers\Services;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller
{
    protected $response;

    protected $model;

    protected $notFoundMessage = 'Page not found';

    public function initialize()
    {
        $this->view->disable();
        $this->response = new \Phalcon\Http\Response;
    }

    final function getPost()
    {
        $postJSON = $this->request->getRawBody();
        if (!empty($postJSON)) {
            $postJSON = json_decode($postJSON, true);
        } else {
            $postJSON = [];
        }
        return array_merge($postJSON, $this->request->getPost());
    }

    final function response($responseData = [], $responseCode = 200, $responseMessage = '', $responseHeader= [])
    {
        $this->response->setStatusCode($responseCode, $responseMessage)
                            ->setJsonContent($responseData);
        if (!empty($responseHeader)) {
            foreach($responseHeader as $headerName => $headerValue) {
                $this->response->setHeader($headerName, $headerValue);
            }
        }
        return $this->response;
    }

    protected function error404($message = 'Page not found')
    {
        return $this->response(['error' => 1, 'message' => $message], 404, $message);
    }
}
