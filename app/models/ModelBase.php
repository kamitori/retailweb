<?php
namespace RW\Models;

use Phalcon\Mvc\Model;

class ModelBase extends Model {

    /**
     *
     * @var timestamp
     */
    public $created_at;

    /**
     *
     * @var timestamp
     */
    public $updated_at;

    public function getId()
    {
        return $this->id;
    }

    public function beforeCreate()
    {
        $this->created_at = $this->updated_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function getMessages($filter = NULL)
    {
        $messages = array();
        foreach (parent::getMessages() as $message) {
            switch ($message->getType()) {
                case 'InvalidCreateAttempt':
                    $messages[] = 'The record cannot be created because it already exists.';
                    break;
                case 'InvalidUpdateAttempt':
                    $messages[] = 'The record cannot be updated because it already exists.';
                    break;
                case 'PresenceOf':
                    $messages[] = 'The field ' . $message->getField() . ' is required.';
                    break;
                default:
                    $messages[] = $message->getMessage();
                    break;
            }
        }

        return $messages;
    }

}
