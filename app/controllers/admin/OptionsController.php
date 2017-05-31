<?php
namespace RW\Controllers\Admin;

use RW\Models\Configs;

class OptionsController extends ControllerBase {

    protected $notFoundMessage = 'This option did not exist.';

    public function listAction()
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
        $columns = ['RW\Models\Options.id', 'RW\Models\Options.name', 'RW\Models\Options.image','RW\Models\Options.price','RW\Models\Options.sold_by','RW\Models\Options.oum','RW\Models\Options.option_group'];
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

        $where = !empty($conditions) ? 'WHERE '.$conditions : '';
        $total = $this->modelsManager->executeQuery('SELECT COUNT(RW\Models\Options.id) as total
                                                        FROM RW\Models\Options
                                                          '.$where, $bind)->getFirst()->total;
        $query = 'SELECT '. implode(', ', $columns) .'
                                                        FROM RW\Models\Options 
                                                        '.$where.'
                                                        ORDER BY RW\Models\Options.'.$order.'
                                                        LIMIT '.$limit.'
                                                        OFFSET '.$offset;
        $data = $this->modelsManager->executeQuery($query, $bind);

        if ($data) {
            
            $configs = new Configs;
            $arr_group = $configs->getOptionGroup();

            $data = $data->toArray();
            foreach($data as $key => $value) {
                if (!is_null($value['image'])) {
                    $value['image'] = URL.'/'.$value['image'];
                } else {
                    $value['image'] = '';
                }
                if(isset($arr_group[$value['option_group']]))
                {
                    $value['option_group_text'] = $arr_group[$value['option_group']]['text'];
                }
                else
                {
                    $value['option_group_text'] = '';   
                }
                $data[$key] = $value;
            }
        } else {
            $data = [];
        }

        return $this->response(['error' => 0, 'data' => $data, 'total' => $total]);
    }

    public function editAction($id = 0)
    {
        $filter = new \Phalcon\Filter;
        $option = $this->model->findFirst($filter->sanitize($id, 'int'));

        if ($option) {
            if (!is_null($option->image)) {
                $option->image = URL.'/'.$option->image;
            }
            $configs = new Configs;
            $arr_group = $configs->getOptionGroup();
            $data_unit = $configs->getListUnit();
            $option = $option->toArray();
            $option['group'] = $arr_group;
            $option['unit'] = $data_unit['unit'];
            $option['listUnit'] = $data_unit['listUnit'];

            return $this->response(['error' => 0, 'data' => $option]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        //$data = array_merge(['name' => '', 'description' => '', 'option_group' => '', 'price' => 0], $data);
        if (isset($data['id'])) {
            $option = $this->model->findFirst($filter->sanitize($data['id'], 'int'));
            if ($option) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $option = new $this->model;
            $message = 'has been created';
        }
        $option->name = $filter->sanitize($data['name'], 'string');
        $option->description = $filter->sanitize($data['description'], 'string');
        $option->option_group = $filter->sanitize($data['option_group'], 'int');
        $option->price = $filter->sanitize($data['price'], 'float');
        $option->sold_by = $filter->sanitize($data['sold_by'], 'string');
        $option->oum = $filter->sanitize($data['oum'], 'string');
        if ($this->request->hasFiles() == true) {
            $imagePath = PUBLIC_PATH . DS . 'images' . DS . 'options';
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            foreach($this->request->getUploadedFiles() as $file) {
                if (isImage($file->getType())) {
                    $fileName = $file->getName();
                    $fileExt = $file->getExtension();

                    $fileName = str_replace('.'.$fileExt, '_'.time().'.'.$fileExt, \Phalcon\Text::uncamelize($fileName));

                    if ($file->moveTo($imagePath . DS . $fileName)) {
                        if (isset($option->image) && file_exists(PUBLIC_PATH . DS . $option->image)) {
                            unlink(PUBLIC_PATH . DS . $option->image);
                        }
                        $option->image = 'images/options/'.$fileName;
                    }
                    break;
                }
            }
        }
        if ($option->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Option <b>'.$option->name.'</b> '.$message.' successful.', 'data' => ['id' => $option->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $option->getMessages()];
        }

        return $this->response($arrReturn);
    }

    public function getOptionsAction()
    {
        $arrReturn = ['error' => 0, 'data' => $this->model->getOptions()];

        return $this->response($arrReturn);
    }

}
