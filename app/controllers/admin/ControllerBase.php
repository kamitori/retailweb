<?php
namespace RW\Controllers\Admin;

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

        //Auto load Model class

        $modelClass = 'RW\\Models\\'.str_replace(['RW\\Controllers\\Admin', 'Controller', '\\'], '', get_class($this));
        if (!in_array($modelClass, ['RW\\Models\\Auth', 'RW\\Models\\Index']) ) {
            $this->model = new $modelClass;
        }
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();

        $identity = $this->auth->getIdentity();        
        // If there is no identity available the admin is redirected to index/index
        if (!is_array($identity) && !in_array(strtolower($controllerName), ['auth', 'index'])) {
            $dispatcher->forward(array(
                'namespace'     => 'RW\Controllers\Admin',
                'controller'    => 'Auth',
                'action'        => 'authorize'
            ));
            return false;
        }
    }

    public function listRecords($columns = [], $arrayHandle = null)
    {
        $filter = new \Phalcon\Filter;
        $data = array_merge([
                'search'     => [],
                'pagination' => [
                    'pageNumber' => 1,
                    'pageSize'   => 100,
                    'sort'       => 'asc',
                    'sortName'   => 'id'
                ]
            ], $this->getPost());
        $conditions = [];
        $bind = [];
        foreach($data['search'] as $fieldName => $value) {
            if (is_numeric($value)) {
                if (is_int($value)) {
                    $value = $filter->sanitize($value, 'int');
                } else if (is_float($value)) {
                    $value = $filter->sanitize($value, 'float');
                }
                $conditions[] = "{$fieldName}= :{$fieldName}:";
                $bind[$fieldName] = $value;
            } else if (is_string($value)) {
                $value = $filter->sanitize($value, 'string');
                $conditions[] = "{$fieldName} LIKE :{$fieldName}:";
                $bind[$fieldName] = '%'.$value.'%';
            }
        }
        $conditions = implode(' AND ', $conditions);
        if (is_string($data['pagination']['sortName'])) {
            $order = $data['pagination']['sortName'] .' '. $data['pagination']['sort'];
        } else {
            $order = 'id desc';
        }

        $limit = is_numeric($data['pagination']['pageSize']) ? $data['pagination']['pageSize'] : 100;
        $pageNumber = is_numeric($data['pagination']['pageNumber']) ? $data['pagination']['pageNumber'] : 1;
        $offset = ceil( ($pageNumber-1) * $limit);

        $total = $this->model->count([
                                        'conditions' => $conditions,
                                        'bind'       => $bind,
                                    ]);

        $data = $this->model->find([
                'conditions' => $conditions,
                'bind'       => $bind,
                'order'      => $order,
                'columns'    => $columns,
                'limit'      => $limit,
                'offset'     => $offset
            ]);

        if ($data) {
            $data = $data->toArray();
            $arrayHandleCallable = is_callable($arrayHandle);
            foreach($data as $key => $value) {
                if ($arrayHandleCallable) {
                    $value = $arrayHandle($value);
                }
                $data[$key] = $value;
            }
        } else {
            $data = [];
        }

        return $this->response(['error' => 0, 'data' => $data, 'total' => $total]);
    }

    public function editRecord($id, $handleRecord = null)
    {
        $filter = new \Phalcon\Filter;
        $record = $this->model->findFirst($filter->sanitize($id, 'int'));

        if ($record) {
            if (is_callable($handleRecord)) {
                $record = $handleRecord($record);
            }
            return $this->response(['error' => 0, 'data' => $record->toArray()]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }

    public function deleteAction($id)
    {
        $filter = new \Phalcon\Filter;
        $arrReturn = ['error' => 1];

        $record = $this->model->findFirst($filter->sanitize($id, 'int'));

        if ($record) {
            $record->delete();
            $arrReturn = ['error' => 0, 'message' => 'This record was deleted successful.'];
        } else {
            $arrReturn['message'] = 'This record did not exist.';
        }

        return $this->response($arrReturn);
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
