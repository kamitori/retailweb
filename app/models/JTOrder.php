<?php
namespace RW\Models;

use \RW\Models\JTProduct;

class JTOrder extends MongoBase {

    public function getSource()
    {
        // return 'tb_salesorder_'.date('m');
        return 'tb_salesorder';
    }

    protected static function getCode($field = 'code')
    {
        $lastOrder = self::findFirst([
            'conditions'=>array(
                'deleted'=> false
            ),
            'fields' => [$field],
            'sort'   => ['_id' => -1]            
        ]);
        if(is_object($lastOrder)){
            $lastOrder = $lastOrder->toArray();
        }
        $code = isset($lastOrder[$field]) ? $lastOrder[$field] : 0;
        $y = date('y');
        $m = str_pad(date('m'), 2, '', STR_PAD_LEFT);
        $prefix = "$y-$m-";
        if( strpos($code, $prefix) !== false ){
            $code = (int)str_replace($prefix, '', $code);
        } else {
            $code = 0;
        }
        return $prefix.str_pad(++$code, 4, 0, STR_PAD_LEFT);
    }

    public static function getAddress(&$arrSave, $contact)
    {
        if(!isset($arrSave['invoice_address'][0])){
            $arrSave['invoice_address'][0] = array(
                                              'invoice_country'=>'Canada',
                                              'invoice_country_id'=>'CA',
                                              );
            $addresses_default_key = 0;
            if(isset($contact['addresses_default_key']))
                $addresses_default_key = $contact['addresses_default_key'];
            if(isset($contact['addresses'][$addresses_default_key])){
                foreach($contact['addresses'][$addresses_default_key] as $field => $value){
                    if($field == 'name' || $field == 'deleted') continue;
                    $arrSave['invoice_address'][0]['invoice_'.$field] = $value;
                }
            }
        }
        if(!isset($arrSave['shipping_address'][0]))
            $arrSave['shipping_address'][0] = array(
                                              'shipping_country'=>'Canada',
                                              'shipping_country_id'=>'CA',
                                              );
        $arrSave['invoice_address'][0]['deleted'] = false;
        $arrSave['shipping_address'][0]['deleted'] = false;
    }

    public static function getCompanyInfo(&$arrSave, $contact, $company)
    {
        $arrSave['our_rep']       = isset($company['our_rep']) ? $company['our_rep'] : (isset($arrSave['our_rep']) ? $arrSave['our_rep'] : '');
        $arrSave['our_rep_id']    = isset($company['our_rep_id']) && strlen($company['our_rep_id']) == 24 ? new \MongoId($company['our_rep_id']) : (isset($arrSave['our_rep_id']) ? $arrSave['our_rep_id'] : '');
        $arrSave['our_csr']       = isset($company['our_csr']) ? $company['our_csr'] : (isset($arrSave['our_csr']) ? $arrSave['our_csr'] : '');
        $arrSave['our_csr_id']    = isset($company['our_csr_id']) && strlen($company['our_csr_id']) == 24 ? new \MongoId($company['our_csr_id']) : (isset($arrSave['our_csr_id']) ? $arrSave['our_csr_id'] : '');
        $arrSave['company_name']  = isset($company['name']) ? $company['name'] : '';
        $arrSave['company_id']    = isset($company['_id']) && strlen($company['_id']) == 24 ? new \MongoId($company['_id']) : '';
        $arrSave['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
        $arrSave['contact_id'] = isset($contact['_id']) ? new \MongoId($contact['_id']) : '';
        $arrSave['email'] = isset($contact['email']) ? $contact['email'] : '';
        $arrSave['phone'] = isset($contact['phone']) ? $contact['phone'] : '';

        self::getAddress($arrSave, $contact);
    }

    public static function getTax(&$arrSave)
    {
        $taxper = 5;
        $arrTax = \RW\Models\JTTax::selectList();
        $taxKey = 'AB';
        if (isset($arrSave['shipping_address'][0]['shipping_province_state_id']) && !empty($arrSave['shipping_address'][0]['shipping_province_state_id'])) {
            $taxKey = $arrSave['shipping_address'][0]['shipping_province_state_id'];
        } else if (isset($arrSave['invoice_address'][0]['invoice_province_state_id']) && !empty($arrSave['invoice_address'][0]['invoice_province_state_id'])) {
            $taxKey = $arrSave['invoice_address'][0]['invoice_province_state_id'];
        }
        if(isset($arrTax[$taxKey])){
            $provKey = explode('%', $arrTax[$taxKey]);
            $taxper = $provKey[0];
        }
        $arrSave['taxval'] = $taxper;
        $arrSave['tax'] = $taxKey;
    }

    public static function calSum($products)
    {
        $count_sub_total = 0;
        $count_amount = 0;
        $count_tax = 0;
        if(!empty($products)){
            foreach($products as $pro_key => $pro_data){
                if(!$pro_data['deleted']){
                    if(isset($pro_data['option_for'])&&is_array($products[$pro_data['option_for']])
                        && (isset($products[$pro_data['option_for']]['sell_by'])&&$products[$pro_data['option_for']]['sell_by']=='combination'
                            || (isset($pro_data['same_parent'])&&$pro_data['same_parent']>0)
                            )
                        )
                        continue;
                    if(isset($pro_data['option_for'])
                       && isset($products[$pro_data['option_for']])
                       && ($products[$pro_data['option_for']]['deleted'] || isset($products[$pro_data['option_for']]['option_for']) )
                       )
                        continue;
                    //cộng dồn sub_total
                    if(isset($pro_data['sub_total'])) {
                        $count_sub_total += round((float)$pro_data['sub_total'],2);
                    }
                    //cộng dồn amount
                    if(isset($pro_data['amount']))
                        $count_amount += round((float)$pro_data['amount'],2);
                }
            }
            //tính lại sum tax
            $count_tax = $count_amount - $count_sub_total;
        }
        return ['sum_amount' => $count_amount, 'sum_sub_total' => $count_sub_total, 'sum_tax' => $count_tax];
    }

    public function add($user, $arrData)
    {
        $arrSave = [];
        $cart_data = (new \RW\Cart\Cart)->get();
        $id_is_quotation = 0;
        if(isset($cart_data['order_id']) && $cart_data['order_id']!=''){
            $order = self::findFirst([
                        'conditions'  => [
                            'deleted'       => false,
                            '_id'    => new \MongoId($cart_data['order_id']),
                        ],
                        'sort'      => ['_id' => -1]
                    ]);
            if($order){
                $arrSave = $order->toArray();
                //hien co cac id SO trung voi Id QT
                if(!isset($arrData['order_type']) || $arrData['order_type']!=2){
                    $id_is_quotation = 1;           
                }
            }
            else{ //truong hop nay id la cua QT, neu update QT completed
                $arrSave['code'] = self::getCode(); //when add
                $id_is_quotation = 1;
                
            }
        }else{
            $arrSave['code'] = self::getCode(); //when add
        }
        $company = \RW\Models\JTCompany::findFirst([
                        'conditions' => ['pos_default' => 1],
                        'fields'     => ['contact_default_id','contact_id','our_rep','our_rep_id','our_csr','our_csr_id','name','email','phone','addresses','addresses_default_key', 'sell_category','sell_category_id','pricing','discount', 'sell_category','sell_category_id','pricing','discount', 'net_discount']
                    ]);
        if( is_object($company) ) {
            $company = $company->toArray();
        }
        else {
            $company = [];
        }
        $arrSave = array_merge($arrSave, $arrData);

        self::getCompanyInfo($arrSave, $user, $company);
        self::getTax($arrSave);
        $arrProducts = $arrOptions = [];
        
        if(empty($cart_data['items']))
            return false;
        foreach($cart_data['items'] as $item) {
            $product = JTProduct::findFirst([
                            'conditions' => [
                                'deleted'   => false,
                                '_id'       => $item['_id']
                            ],
                            'fields' => ['_id','code', 'name', 'sku', 'sell_price', 'oum', 'oum_depend', 'sell_by','pricebreaks', 'sellprices', 'unit_price','options', 'products_upload', 'product_desciption', 'is_custom_size']
                        ]);
            if (!is_object($product)) {
                continue;
            }
            $product = $product->toArray();
            $product['sizew']  = 0;
            $product['sizeh'] = 0;
            $product['sizew_unit'] = $product['sizeh_unit'] = 'in';
            if(isset($item['combo_id'])){
                $combo_id = $item['combo_id'];
                $combo_qty = isset($cart_data['combo_list'][$combo_id]['quantity'])?$cart_data['combo_list'][$combo_id]['quantity']:1;
                $product['quantity'] = $item['quantity']*$combo_qty;
            }else
                $product['quantity'] = $item['quantity'];
            $product['custom_unit_price'] = 0;
            if(isset($item['combo_id'])){
                $product['custom_unit_price'] = $item['sell_price']*(1-$item['discount']);
                //Set combo_id to product item
                $product['combo_id'] = $item['combo_id'];
            }
            $productKey = count($arrProducts);
            $plusSellPrice = 0;
            $arrProducts[$productKey] = [
                    'deleted' => false,
                    'products_name' => $product['name'],
                    'products_costing_name' => '',
                    'products_id' => new \MongoId($product['_id']),
                    'option' => '',
                    'sizew' => $product['sizew'],
                    'sizew_unit' => 'in',
                    'sizeh' => $product['sizeh'],
                    'sizeh_unit' => 'in',
                    'receipts' => '',
                    'sell_by' => $product['sell_by'],
                    'sell_price' => $product['sell_price'],
                    'oum' => $product['oum'],
                    'unit_price' => $product['unit_price'],
                    'quantity' => $product['quantity'],
                    'adj_qty' => 0,
                    'sub_total' => 0,
                    'taxper' => $arrSave['taxval'],
                    'use_tax' => 1,
                    'amount' => 0,
                    'sku' => isset($product['sku']) ? $product['sku'] : '',
                    'code' => $product['code'],
                    'is_custom_size' => isset($product['is_custom_size']) ? $product['is_custom_size'] : 0,
                    'custom_unit_price' => $product['custom_unit_price'],
                    'tax' => $arrSave['taxval'],
                    'plus_sell_price' => 0,
                    'oum_depend' => $product['oum_depend'],
                    'area' => 0,
                    'perimeter' => 0,
                    'company_price_break' => false,
                    'vip' => 0,
                    'plus_unit_price' => 0,
                    'combo_id' => isset($product['combo_id'])?$product['combo_id']:''
            ];
            //=============Cal-Bleed=============
            $lineBleed = JTProduct::calBleed($product, true);
            if( !empty($lineBleed) ){
                $product['bleed_sizew'] = $lineBleed['bleed_sizew'];
                $product['bleed_sizeh'] = $lineBleed['bleed_sizeh'];
            } else {
                $product['bleed_sizew'] = $product['bleed_sizeh'] = 0;
            }
            //=============End Cal-Bleed=========
            //=============Loop option bleed=============
            if( isset($product['options']) ) {
                foreach($product['options'] as $option){
                    if(isset($option['deleted']) && $option['deleted']) continue;
                    if( !isset($option['product_id']) || !is_object($option['product_id']) ) continue;
                    if(isset($option['same_parent'])&&$option['same_parent']){
                            $option['_id'] = $option['product_id'];
                            $option['sizew'] = $product['sizew'];
                            $option['sizeh'] = $product['sizeh'];
                            $option['sizew_unit'] = $option['sizeh_unit'] = 'in';
                            $optionBleed = JTProduct::calBleed($option, true);
                            if( !empty($optionBleed) ) {
                                $product['bleed_sizew'] += $optionBleed['bleed_sizew'];
                                $product['bleed_sizeh'] += $optionBleed['bleed_sizeh'];
                            }
                    }
                }
            }
            //=============Loop option bleed=========
            //=============Check bleed=============
            if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
            if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
            //=============End Check bleed=========
            if( isset($product['options']) ) {
                foreach($product['options'] as $option){
                    if(isset($option['deleted']) && $option['deleted']) continue;
                    if( !isset($option['product_id']) || !is_object($option['product_id']) ) continue;
                    $optionKey = count($arrOptions);
                    $tmpOpt = JTProduct::findFirst([
                            'conditions' => [
                                'deleted'   => false,
                                '_id'       => new \MongoId($option['product_id'])
                            ],
                            'fields' => ['sku', 'code', 'name', 'sell_price','sell_by', 'pricebreaks', 'sellprices']
                        ]);
                    if (!is_object($tmpOpt)) {
                        continue;
                    }
                    $tmpOpt = $tmpOpt->toArray();
                    $tmpOpt['price_break'] = JTProduct::priceBreak($tmpOpt, $company);
                    $option = array_merge($option, $tmpOpt);
                    unset($tmpOpt);
                    if( !isset($option['same_parent']) )
                        $option['same_parent'] = 0;
                    else
                        $option['same_parent'] = 1;
                    $option['sizew'] = $product['sizew'];
                    $option['sizeh'] = $product['sizeh'];
                    $option['sizew_unit'] = $product['sizew_unit'];
                    $option['quantity'] = 1;
                    $option['price_break'] = JTProduct::priceBreak($option, $company);
                    $optionChoice = false;
                    foreach ($item['options'] as $optKey => $opt) {
                        if ($opt['quantity'] == 0) continue;
                        if ($opt['_id'] != $option['_id']) continue;
                        $option = array_merge($option, $opt);
                        $optionChoice = true;
                        unset($item['options'][$optKey]);
                    }
                    if( $option['same_parent'] ){
                        if( isset($product['bleed_sizew']) ) {
                            $option['bleed_sizew'] = $product['bleed_sizew'];
                        }
                        if( isset($product['bleed_sizeh']) ) {
                            $option['bleed_sizeh'] = $product['bleed_sizeh'];
                        }
                        $tmpOpt = $option;
                        $tmpOpt['quantity'] *= $product['quantity'];
                        $tmpOpt['use_tax'] = 1;
                        JTProduct::calPrice($tmpOpt);
                        $option['sell_price'] = $tmpOpt['sell_price'];
                        unset($tmpOpt);
                    }
                    $option['use_tax'] = 1;
                    JTProduct::calPrice($option);
                    if( !$option['same_parent'] && isset($company['net_discount']) ){
                        $tmpPrice = $option['sell_price'];
                        JTProduct::netDiscount($option['sub_total'], $company['net_discount']);
                        JTProduct::netDiscount($option['sell_price'], $company['net_discount']);
                        $option['unit_price'] = $option['sell_price'];
                    }
                    if(  $optionChoice ){
                        if( $option['same_parent'] ){
                            $plusSellPrice += $option['sub_total'];
                        }
                        JTProduct::calTax($option);
                        JTProduct::calAmount($option);
                        $lineProductKey = count($arrProducts);
                        $arrProducts[$lineProductKey] = [
                                'deleted' => false,
                                'code' => $option['code'],
                                'sku' => $option['sku'],
                                'products_name' => $option['name'],
                                'product_name' => $option['name'],
                                'products_id' => new \MongoId($option['_id']),
                                'product_id' => new \MongoId($option['_id']),
                                'quantity' => $option['quantity'],
                                'sub_total' => $option['sub_total'],
                                'option_group' => isset($option['option_group']) ? $option['option_group'] : '',
                                'sizew' => $option['sizew'],
                                'sizew_unit' => 'in',
                                'sizeh' => $option['sizeh'],
                                'sizeh_unit' => 'in',
                                'sell_by' => $option['sell_by'],
                                'oum' => $option['oum'],
                                'same_parent' => $option['same_parent'],
                                'sell_price' => $option['sell_price'],
                                'taxper' => $arrSave['taxval'],
                                'tax' => $option['tax'],
                                'use_tax'=>1,
                                'option_for' => $productKey,
                                'proids' => $option['_id'].'_'.$optionKey,
                                'adj_qty' => $option['adj_qty'],
                                'oum_depend' => $option['oum_depend'],
                                'plus_sell_price' => 0,
                                'plus_unit_price' => 0,
                                'unit_price' => $option['unit_price'],
                                'amount' => $option['amount'],
                                'area' => $option['area'],
                                'perimeter' => $option['perimeter'],
                                'company_price_break' => false,
                                'user_custom' => 0,
                                'hidden' => isset($option['hidden']) ? $option['hidden'] : 0,
                        ];
                        if( !$option['same_parent'] && isset($tmpPrice) ) {
                            $arrProducts[$lineProductKey]['custom_unit_price'] = $arrProducts[$lineProductKey]['sell_price'];
                            $arrProducts[$lineProductKey]['sell_price'] =
                                        $arrProducts[$lineProductKey]['unit_price'] = $tmpPrice;
                            unset($tmpPrice);
                        }
                    }
                    $arrOptions[$optionKey] = [
                                    'deleted'    => false,
                                    'product_id' => new \MongoId($option['_id']),
                                    '_id' => new \MongoId($option['_id']),
                                    'markup'     => 0,
                                    'margin'     => 0,
                                    'quantity'   => $option['quantity'],
                                    'option_group'   => isset($option['option_group']) ? $option['option_group'] : '',
                                    'require'    => isset($option['require']) ? $option['require'] : 0,
                                    'same_parent'    => $option['same_parent'],
                                    'unit_price'     => $option['unit_price'],
                                    'oum'    => $option['oum'],
                                    'product_name'   => $option['name'],
                                    'sku'    => $option['sku'],
                                    'code'   => $option['code'],
                                    'sizew'  => $option['sizew'],
                                    'sizew_unit'     => $option['sizew_unit'],
                                    'sizeh'  => $option['sizeh'],
                                    'sizeh_unit'     => $option['sizeh_unit'],
                                    'sell_by'    => $option['sell_by'],
                                    'discount'   => 0,
                                    'sub_total'  => $option['sub_total'],
                                    'this_line_no'   => $optionKey,
                                    'choice'    => $optionChoice ? 1 : 0,
                                    'user_custom'    => 0,
                                    'use_tax'=>1,
                                    'sell_price'     => $option['sell_price'],
                                    'parent_line_no' => $productKey

                    ];
                    if( isset($lineProductKey) ){
                        $arrOptions[$optionKey]['line_no'] = $lineProductKey;
                        unset($lineProductKey);
                    }
                }
            }
            //=============Check bleed=============
            if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
            if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
            //=============End Check bleed=========
            $product['plus_sell_price'] = $plusSellPrice;
            $product['price_break'] = JTProduct::priceBreak($product, $company);
            $product['use_tax'] = 1;
            JTProduct::calPrice($product);
            if( isset($company['net_discount']) ){
                $tmpPrice = $product['sell_price'];
                JTProduct::netDiscount($product['sub_total'], $company['net_discount']);
                JTProduct::netDiscount($product['sell_price'], $company['net_discount']);
                $product['unit_price'] = $product['sell_price'];
            }
            $arrProducts[$productKey]['unit_price'] =
                        $arrProducts[$productKey]['sell_price'] = $product['sell_price'];
            if( isset($product['bleed']) ) {
                $arrProducts[$productKey]['bleed'] = true;
            }
            if( isset($tmpPrice) ) {
                $arrProducts[$productKey]['unit_price'] =
                        $arrProducts[$productKey]['sell_price'] = $tmpPrice;
                unset($tmpPrice);
            }
            $arrProducts[$productKey]['sub_total'] = $product['sub_total'];
            $arrProducts[$productKey]['plus_unit_price'] = $plusSellPrice;
            $arrProducts[$productKey]['adj_qty'] = $product['adj_qty'];
            $arrProducts[$productKey]['amount'] = $product['sub_total'];
            $arrProducts[$productKey]['perimeter'] = $product['perimeter'];
            if($arrProducts[$productKey]['custom_unit_price']==0)
                $arrProducts[$productKey]['custom_unit_price'] = $product['sell_price'];
            $arrProducts[$productKey]['area'] = $product['area'];
            JTProduct::calTax($arrProducts[$productKey]);
            JTProduct::calAmount($arrProducts[$productKey]);
        }
        //ket thuc array product 
        if(isset($cart_data['discount_total']) && $cart_data['discount_total']>0)
        $arrProducts[] = array(
                'deleted' => false,
                'code' => 192,
                'sku' => "OPT-192",
                'products_name' => "Discount",
                'product_name' => "Discount",
                'products_id' => new \MongoId('574506db124dca181c7e409c'),
                'product_id' => new \MongoId('574506db124dca181c7e409c'),
                'quantity' => 1,
                'sub_total' => (-1*$cart_data['discount_total']),
                'sizew' => 0,
                'sizew_unit' => 'in',
                'sizeh' => 0,
                'sizeh_unit' => 'in',
                'oum' => 'unit',
                'sell_price' => (-1*$cart_data['discount_total']),
                'sell_by' => 'unit',
                'unit_price' => 0,
                'taxper' => 5,
                'tax' => (-1*$cart_data['discount_total']*0.05),
                'adj_qty' => 0,
                'oum_depend' => $option['oum_depend'],
                'plus_sell_price' => 0,
                'plus_unit_price' => 0,
                'amount' => (-1*$cart_data['discount_total'])+(-1*$cart_data['discount_total']*0.05),
                'is_custom_size'=> 0,
                'custom_unit_price' => (-1*$cart_data['discount_total']),
                'plus_sell_price'=> 0,
                'origin_options'=> array()
            );



        $arrSave['products'] = $arrProducts;
        $arrSave['options'] = $arrOptions;
        $arrSum = self::calSum($arrSave['products']);
        $arrSave = array_merge($arrSave, $arrSum);
        $arrSave['refund'] = $arrSave['cash_tend'] - $arrSave['sum_amount'];
        if(isset($arrSave['order_type']) && $arrSave['order_type'] == 2 && isset($arrSave['had_paid_amount']) && $arrSave['had_paid_amount']!=$arrSave['sum_amount']){
            $arrSave['had_paid'] = 0;
        }
        $arrSave['name'] = $arrSave['code'].' - '.$arrSave['company_name'];
        $arrSave['heading'] = 'Create from POS';
        $arrSave['create_from'] = 'Create from POS';
        $job = (new \RW\Models\JTJob)->todayJob();
        $arrSave['job_number'] = $job->no;
        $arrSave['job_name'] = $job->name;
        $arrSave['job_id'] = $job->_id;
        $this->__default($arrSave);
        $arrSave['cart'] = $cart_data;
        $arrSave['other_comment'] = $cart_data['note']?$cart_data['note']:'';

        $arrSave['manager_completed'] = 0;

        if($id_is_quotation == 1){
            $arrSave = (new \RW\Models\JTQuotation)->comletedQuotation($cart_data['order_id'],$arrSave);
        }

        //Update
        if(isset($cart_data['order_id']) && $cart_data['order_id']!='' && $id_is_quotation == 0){
            $this->getConnection()->{$this->getSource()}->update(array('_id'=>new \MongoId($cart_data['order_id'])), $arrSave);
            return $cart_data['order_id'].'%'.$arrSave['code'];
        //Add new
        }else{ 
            $this->getConnection()->{$this->getSource()}->insert($arrSave);
            return $arrSave['_id'].'%'.$arrSave['code'];
        }
    }

    public function getLast($user)
    {
        if (!isset($user['_id'])) {
            return false;
        }
        $lastOrder = self::findFirst([
                        'conditions'  => [
                            'deleted'       => false,
                            'contact_id'    => $user['_id'],
                        ],
                        'sort'      => ['code' => -1],
                        'fields'    => ['products', 'options']
                    ]);
        if (!$lastOrder) {
            return false;
        }
        $lastOrder = $lastOrder->toArray();
        $items = [];
        $products = array_filter($lastOrder['products'], function ($array) {
            return (!isset($array['deleted']) || !$array['deleted']) && !isset($array['option_for']);
        });
        foreach ($products as $productKey => $product) {
            $p = \RW\Models\JTProduct::findFirst([
                        'conditions' => [
                            'deleted'   => false,
                            '_id'       => $product['products_id']
                        ],
                        'fields' => ['name', 'description', 'products_upload']
                    ]);
            if (!$p) {
                continue;
            }
            $image = '';
            if (!empty($p->products_upload)) {
                $image = array_filter($p->products_upload, function($array) {
                    return (!isset($array['deleted']) || !$array['deleted']) && !empty($array['path']);
                });
                $image = reset($image);
                $image = JT_URL.'/'.$image['path'];
            }
            $item = [
                '_id'        => $product['products_id'],
                'name'       => $p['name'],
                'image'      => $image,
                'description'=> $p['description'],
                'sell_price' => $product['custom_unit_price'],
                'quantity'   => $product['quantity'],
                'total'      => $product['sub_total'],
            ];

            $itemOptions = [];

            $options = array_filter($lastOrder['options'], function ($array) use ($productKey) {
                return (!isset($array['deleted']) || !$array['deleted']) && $array['parent_line_no'] == $productKey;
            });

            foreach ($options as $option) {
                $quantity = 0;
                $p = \RW\Models\JTProduct::findFirst([
                    'conditions' => [
                        'deleted'   => false,
                        '_id'       => $option['product_id']
                    ],
                    'fields' => ['name']
                ]);
                if (!$p) {
                    continue;
                }
                if (isset($option['line_no']) && isset($lastOrder['products'][$option['line_no']])) {
                    $quantity = $lastOrder['products'][$option['line_no']]['quantity'];
                }
                $itemOptions[] = [
                    '_id'       => $option['product_id'],
                    'name'      => $p['name'],
                    'quantity'  => $quantity,
                ];
            }
            $item['options'] = $itemOptions;
            $items[] = $item;
        }
        return $items;
    }

    protected function getDefault()
    {
        return [
                'deleted'   => 'bool',
                'options'   => 'array',
                'salesorder_date' => 'date',
                'payment_due_date' => 'date',
                'payment_terms' => ['default' => 0],
                'status'    => ['default' => 'In production'],
                'status_id'    => ['default' => 'In production'],
                'asset_status'    => ['default' => 'In production'],
                'sales_order_type'  => ['default' => 'Sales Order'],
                'job_id'        => 'string',
                'job_name'      => 'string',
                'job_number'    => 'string',
                'quotation_id'      => 'string',
                'quotation_name'    => 'string',
                'quotation_number'  => 'string',
                'customer_po_no'    => 'string',
                'delivery_method'   => 'string',
                'shipper'       => 'string',
                'shipper_account'   => 'string',
                'shipper_id'    => 'string',
        ];
    }

    public function getPosList(){
        $orderList = null;
        $orderList = self::find([
                        'conditions'  => [
                            'deleted'       => false,
                            'pos_delay'    => 1,
                        ],
                        'sort'      => ['code' => -1]
                    ]);
        return $orderList;
    }
    public function updateOneOrder($order_id, $arrSave){
        if(isset($order_id) && $order_id!='' && isset($arrSave)){
            $Order = self::findFirst([
                        'conditions'  => [
                            '_id'    => new \MongoId($order_id),
                        ],
                        'sort'      => ['code' => -1]
                    ])->toArray();
            // pr($Order);die;
            $arrSave = array_merge($Order,$arrSave);
            $this->getConnection()->{$this->getSource()}->update(array('_id'=>new \MongoId($order_id)), $arrSave, array('upsert' => true));
            return true;
        }
        return false;
    }

    public function reprocessOrder($order_id, $arrSave){
        if(isset($order_id) && $order_id!='' && isset($arrSave)){
            $Order = self::findFirst([
                        'conditions'  => [
                            '_id'    => new \MongoId($order_id),
                        ],
                        'sort'      => ['code' => -1]
                    ])->toArray();
            // pr($Order);die;
            $arrSave = array_merge($Order,$arrSave);
            if(isset($arrSave['cart']['items'])){
                foreach ($arrSave['cart']['items'] as $key => $value) {
                    if(isset($arrSave['cart']['items'][$key]['multi_compl']))
                        unset($arrSave['cart']['items'][$key]['multi_compl']);
                    if(isset($arrSave['cart']['items'][$key]['completed']))
                        unset($arrSave['cart']['items'][$key]['completed']);
                }
            }
            if(isset($arrSave['completed']))
                $arrSave['completed'] = 0;
            
            $arrSave['status'] = "In production";
            $arrSave['status_id'] = "In production";
            $arrSave['asset_status'] = "In production";
            $arrSave['datetime_pickup'] = new \MongoDate(time());
            
            $this->getConnection()->{$this->getSource()}->update(array('_id'=>new \MongoId($order_id)), $arrSave, array('upsert' => true));
            return true;
        }
        return false;
    }

    
}
