<?php
namespace RW\Controllers\Admin;
use RW\Models\ProductOption;
class ProductsController extends ControllerBase {

    protected $notFoundMessage = 'This product did not exist.';

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
        $conditions = [
            'deleted'       => false,
            'assemply_item' => 1
        ];
        foreach($data['search'] as $fieldName => $value) {
            if (is_numeric($value)) {
                if (is_int($value)) {
                    $value = $filter->sanitize($value, 'int');
                } else if (is_float($value)) {
                    $value = $filter->sanitize($value, 'float');
                }
                $conditions[$fieldName] = $value;
            } else if (is_string($value)) {
                $value = $filter->sanitize($value, 'string');
                $conditions[$fieldName] = new \MongoRegex('/'.$value.'/i');
            }
        }
        if (is_string($data['pagination']['sortName'])) {
            $order = [$data['pagination']['sortName'] => ($data['pagination']['sort'] == 'asc' ? 1 : -1)];
        } else {
            $order = ['_id' => 1];
        }

        $limit = is_numeric($data['pagination']['pageSize']) ? $data['pagination']['pageSize'] : 100;
        $pageNumber = is_numeric($data['pagination']['pageNumber']) ? $data['pagination']['pageNumber'] : 1;
        $offset = ceil( ($pageNumber-1) * $limit);

        $data = \RW\Models\JTProduct::find([
                                            $conditions,
                                            'fields' => ['_id', 'code', 'name', 'sell_price', 'category', 'products_upload', 'description'],
                                            'sort'   => $order,
                                            'limit'  => $limit,
                                            'skip'   => $offset
                                        ]);


        if ($data) {
            foreach($data as $key => $value) {
                $value = $value->toArray();
                if (isset($value['products_upload']) && !empty($value['products_upload'])) {
                    $image = reset(array_filter($value['products_upload'], function($array) {
                                    return isset($array['deleted']) && !$array['deleted'];
                                }));
                    $value['image'] = isset($image['image']) ? URL.'/'.$image['image'] : '';
                } else {
                    $value['image'] = '';
                }
                $value['id'] = (string)$value['_id'];
                $data[$key] = $value;
            }
        } else {
            $data = [];
        }

        return $this->response(['error' => 0, 'data' => $data, 'total' => $total]);
    }

    public function editAction($id = '')
    {
        $filter = new \Phalcon\Filter;
        $product = \RW\Models\JTProduct::findById($filter->sanitize($id, 'alphanum'));

        if ($product) {
            $product = $product->toArray();
            if (isset($value['products_upload']) && !empty($value['products_upload'])) {
                $image = reset(array_filter($value['products_upload'], function($array) {
                                return isset($array['deleted']) && !$array['deleted'];
                            }));
                $product['image'] = isset($image['image']) ? URL.'/'.$image['image'] : '';
            } else {
                $product['image'] = '';
            }
            $product['_id'] = (string)$product['_id'];
            $product['sell_price'] = (float)$product['sell_price'];
            $product['categoryOptions'] = (new \RW\Models\JTSettings)->type('product_category')->getSelect();
            $product['productOptions'] = (new \RW\Models\JTProduct)->getOptions();
            $options = [];
            if (isset($product['options'])) {
                foreach ($product['options'] as $key => $option) {
                    $p = (new \RW\Models\JTProduct)->findFirst([
                                    ['_id' => new \MongoId($option['product_id'])],
                                    'fields'    => ['name', 'code', 'sku']
                                ]);
                    if ($p) {
                        $p = $p->toArray();
                    } else {
                        $p = [];
                    }
                    $option = array_merge([
                            'name' => '', 'sku' => '', 'code' => '', 'sell_price' => 0
                        ], $option, $p);
                    $option['product_id'] = (string)$option['product_id'];
                    if (!isset($option['unit_price'])) {
                        $option['unit_price'] = (float)$option['sell_price'];
                    }
                    $options[] = $option;
                    $i++;
                }
            }
            $product['options'] = $options;
            return $this->response(['error' => 0, 'data' => $product]);
        } else {
            return $this->error404($this->notFoundMessage);
        }
    }

    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();

        $data = array_merge(['name' => '', 'description' => '', 'category_id' => 0, 'meta_title' => '', 'meta_description' => ''], $data);
        if (isset($data['_id'])) {
            $product = \RW\Models\JTProduct::findById($filter->sanitize($data['_id'], 'alphanum'));
            if ($product) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
        } else {
            $product = new $this->model;
            $message = 'has been created';
        }
        $product->name = $data['name'];
        $product->description = $data['description'];
        $product->category = $filter->sanitize($data['category'], 'string');
        $product->sell_price = $filter->sanitize($data['sell_price'], 'float');
        $product->meta_title = $filter->sanitize($data['meta_title'], 'string');
        $product->meta_description = $filter->sanitize($data['meta_description'], 'string');

        $options = json_decode($data['options'], true);
        foreach($options as $key => $option) {
            foreach (['deleted', 'required', 'same_parent'] as $field) {
                if (isset($option[$field]) && $option[$field]) {
                    $option[$field] = true;
                } else {
                    $option[$field] = false;
                }
            }
            if (is_string($option['product_id']) && strlen($option['product_id']) == 24) {
                $option['product_id'] = new \MongoId($option['product_id']);
            }
            $option['unit_price'] = (float)$option['unit_price'];
            unset($option['_id']);
            $options[$key] = $option;
        }

        $product->options = $options;

        if ($product->save() === true) {
            $arrReturn = ['error' => 0, 'message' => 'Product <b>'.$product->name.'</b> '.$message.' successful.', 'data' => ['_id' => $product->getId()]];
        } else {
            $arrReturn = ['error' => 1, 'messages' => $product->getMessages()];
        }

        return $this->response($arrReturn);
    }

}
