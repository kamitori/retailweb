<?php
namespace RW\Controllers\Admin;

class AdminsController extends ControllerBase {

    protected $notFoundMessage = 'Admin did not exist.';

    public function listAction()
    {
       return $this->listRecords(['id', 'name', 'email']);
    }

    public function editAction($id = 0)
    {
        $filter = new \Phalcon\Filter;
        $record = $this->model->findFirst([
                            'id = '.$filter->sanitize($id, 'int'),
                            'columns'   => ['id', 'name', 'email']
                        ]);

        if ($record) {
            return $this->response(['error' => 0, 'data' => $record->toArray()]);
        } else {
            return $this->error404($this->notFoundMessage);
        }

    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['name' => '', 'email' => '', 'password'], $data);
        if (isset($data['id'])) {
            $admin = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($admin) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $admin = new $this->model;
            $message = 'has been created';
        }
        $admin->name = $filter->sanitize($data['name'], 'string');
        $admin->email = $filter->sanitize($data['email'], 'email');

        $validation = new \Phalcon\Validation;
        if ( (isset($admin->id) && !empty($data['password']))
                || !isset($admin->id) ) {
            $validation->add(
                'password',
                new \Phalcon\Validation\Validator\PresenceOf(
                    array(
                        'message' => 'The password is required'
                    )
                )
            );

            $validation->add(
                'password',
                new \Phalcon\Validation\Validator\Confirmation(
                    array(
                        'message'   => 'The e-mail is required',
                        'with'      => 'password_confirm'
                    )
                )
            );
        }


        if (count($validation->validate($data))) {
            $messages = [];
            foreach ($validation->getMessages() as $message) {
                switch ($message->getType()) {
                    case 'InvalidCreateAttempt':
                        $messages[] = 'The record cannot be created because it already exists.';
                        break;
                    case 'InvalidUpdateAttempt':
                        $messages[] = 'The record cannot be updated because it already exists.';
                        break;
                    case 'PresenceOf':
                        $messages[] = 'The field ' . $message->getField() . ' is required';
                        break;
                    case 'Confirmation':
                        $messages[] = $message->getField() . ' must match.';
                        break;
                    default:
                        $messages[] = $message->getMessage();
                        break;
                }
            }
            return $this->response(['error' => 1, 'messages' => $messages]);
        }

        if (!empty($data['password'])) {
            $admin->password = $this->security->hash($data['password']);
        }


        if ($admin->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Admin <b>'.$admin->name.'</b> '.$message.' successful.', 'data' => ['id' => $admin->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $admin->getMessages()];
        }

        return $this->response($arrReturn);
    }
}
