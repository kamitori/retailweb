<?php
namespace RW\Models;
use RW\UnitConvertor\MyUnitConverter as MyUnitConverter;

class JTProduct extends MongoBase {

    public $code = '';
    public $name = '';

    public function getSource()
    {
        return 'tb_product';
    }

    public function getOptions()
    {
        $options = $this->find([
            [
                'deleted'       => false,
                'assemply_item' => 1,
                'product_type'  => 'Options'
            ],
            'fields' => ['_id', 'name', 'sku', 'code', 'sell_price'],
            'sort'   => ['name' => 1]
        ]);
        foreach ($options as $key => $option) {
            $option = $option->toArray();
            $option = array_merge([
                            'name' => '', 'sku' => '', 'code' => '', 'sell_price' => 0
                        ], $option);
            $option['_id'] = (string)$option['_id'];
            $option['unit_price'] = (float)$option['sell_price'];
            $options[$key] = $option;
        }
        return $options;
    }

    public static function convert($arrData)
    {
        $arrData = self::setDefault($arrData);
        require_once app_path().'/common/MyUnitConverter.php';
        $unitConverter = new MyUnitConverter;
        if( in_array($arrData['sell_by'], ['area', 'permeter']) ){
            $arrData['sizew'] = $unitConverter->myConvert($arrData['sizew'], $arrData['sizew_unit'], 'in', 2);
            $arrData['sizew_unit'] = 'in';
            $arrData['sizeh'] = $unitConverter->myConvert($arrData['sizeh'], $arrData['sizeh_unit'], 'in', 2);
            $arrData['sizeh_unit'] = 'in';
        }
        return $arrData;
    }

    public static function companyPricebreak($company, $productId)
    {
        $result = ['price_break' =>[], 'sell_category_key' => '', 'discount' => 0];
        if( isset($company['pricing']) ){
            if( is_object($productId) )
                $productId = (string)$productId;
            foreach($company['pricing'] as $value){
                if( isset($value['deleted']) && $value['deleted'] ) continue;
                if( !isset($value['product_id']) || (string)$value['product_id'] != $productId ) continue;
                if( !isset($value['price_break']) ) continue;
                foreach($value['price_break'] as $v){
                    if( isset($value['deleted']) && $value['deleted'] ) continue;
                        $result['price_break'][] = $v;
                }
            }
        }

        if(isset($company['sell_category_id']))
            $result['sell_category_key'] = $company['sell_category_id'];
        if(isset($company['discount']))
            $result['discount'] = (float)$company['discount'];
        return $result;
    }

    public static function productPricebreak($arrData, $sell_category_key = '')
    {
        $result = [];
        if( isset($arrData['sellprices']) ){
            $result['sell_price'] = '';
            $sell_price_default = 0;
            foreach($arrData['sellprices'] as $value){
                if( isset($value['deleted']) && $value['deleted'] ) continue;
                if( !isset($value['sell_category']) ) continue;
                if($sell_category_key!='' && $value['sell_category'] == $sell_category_key)
                    $result['sell_price'] = $value['sell_unit_price'];
                if( isset($value['sell_default']) && (int)$value['sell_default']==1){
                    $sell_price_default = $value['sell_unit_price'];
                    $sell_category_key_df = $value['sell_category'];
                }
            }
            if($result['sell_price'] == '' && $sell_price_default!='' ){
                $result['sell_price'] = $sell_price_default;
                $sell_category_key = $sell_category_key_df;
            }

        }else if(isset($arrData['sell_price'])){
            $result['sell_price'] = $arrData['sell_price'];
        }
        //tim Price breaks
        if(isset($arrData['pricebreaks']) && is_array($arrData['pricebreaks']) && count($arrData['pricebreaks'])>0){
            foreach($arrData['pricebreaks'] as $key => $value){
                if( isset($value['deleted']) && $value['deleted'] ) continue;
                if( !isset($value['sell_category']) ) continue;
                if( $value['sell_category'] != $sell_category_key ) continue;
                $result['price_break'][$key] = $value;
            }
        }
        return $result;
    }

    public static function priceBreak($arrData, $company)
    {
        $result = [];
        $sell_category = self::companyPricebreak($company,$arrData['_id']);
        if(isset($sell_category['price_break']) && count($sell_category['price_break'])>0)
            $result['company_price_break'] = $sell_category['price_break'];
        if(isset($sell_category['discount']) && $sell_category['discount']!='')
            $result['discount'] = $sell_category['discount'];
        if(!isset($sell_category['sell_category_key']))
            $sell_category['sell_category_key'] = '';
        $sell_break = self::productPricebreak($arrData, $sell_category['sell_category_key']);
        if(isset($sell_break['sell_price']))
            $result['sell_price'] = $sell_break['sell_price'];
        else
            $result['sell_price'] = 0;
        if(isset($sell_break['sell_price_plus']) && $sell_break['sell_price_plus']!='')
            $result['sell_price_plus'] = $sell_break['sell_price_plus'];

        if(isset($sell_break['price_break']) && count($sell_break['price_break'])>0)
            $result['product_price_break'] = $sell_break['price_break'];
        return $result;
    }

    public static function calPriceBreak(&$arrData)
    {
        $price_break = isset($arrData['price_break']) ? $arrData['price_break'] : [];
        if( isset($price_break['company_price_break']) ){
            usort($price_break['company_price_break'], function($a, $b){
                return $a['range_from'] > $b['range_from'];
            });
            foreach($price_break['company_price_break'] as $keys=>$value){
                if($arrData['adj_qty']<=(float)$value['range_to'] && $arrData['adj_qty']>=(float)$value['range_from']){
                    //neu thoa dieu kien
                    if(!isset($value['unit_price']))
                        $value['unit_price'] = 0;
                    $arrData['sell_price'] = (float)$value['unit_price'];
                    return true;
                }
            }
        }
        if( isset($price_break['product_price_break']) ){
            usort($price_break['product_price_break'], function($a, $b){
                return $a['range_from'] > $b['range_from'];
            });
            foreach($price_break['product_price_break'] as $keys=>$value){
                if($arrData['adj_qty']<=(float)$value['range_to'] && $arrData['adj_qty']>=(float)$value['range_from']){
                    if(!isset($value['unit_price']))
                        $value['unit_price'] = 0;
                    $arrData['sell_price'] = (float)$value['unit_price'];
                    self::discount($arrData); //và tính discount
                    $arrData['price_break_from_to'] = $price_break; //luu lai bang price_break da sort
                    return true;
                }
            }
        }
        if(isset($price_break['sell_price'])){
            $arrData['sell_price'] = (float)$price_break['sell_price'];
            self::discount($arrData); //và tính discount
            return true;
        }
    }

    private static function setDefault($arrData, $defaultUnitLength = 'ft')
    {
        $lenghKey=array(
            'Sq. ft.'   => 'ft',
            'Sq.ft.'    => 'ft',
            'Sq.in.'    => 'in',
            'Sq.cm.'    => 'cm',
            'Sq.m.'     => 'm',
            'Sq.yard'   => 'yard',
            'Sq.mm.'    => 'mm',
        ); //default

        $permeterKey=array(
            'Lr. ft.'   => 'ft',
            'Lr. in.'   => 'in',
            'Lr. cm.'   => 'cm',
            'Lr. m.'    => 'm',
            'Lr. yard'  => 'yard',
            'Lr. mm.'   => 'mm',
        ); //default
        if(!isset($arrData['sizew_unit']) || $arrData['sizew_unit']=='')
            $arrData['sizew_unit'] = 'in';
        //sizeh_unit
        if(!isset($arrData['sizeh_unit']) || $arrData['sizeh_unit']=='')
            $arrData['sizeh_unit'] = 'in';
        //sizew
        if(isset($arrData['sizew']))
            $arrData['sizew'] = (float)$arrData['sizew'];
        else
            $arrData['sizew'] = 0;
        //sizeh
        if(isset($arrData['sizeh']))
            $arrData['sizeh'] = (float)$arrData['sizeh'];
        else
            $arrData['sizeh'] = 0;

        //oum
        if(!isset($arrData['oum']))
            $arrData['oum'] = 'unit';

        //oum_depend
        if(!isset($arrData['oum_depend']))
            $arrData['oum_depend'] = 'unit';
        if(isset($arrData['oum_depend']) && $arrData['oum_depend']=='Sq. ft.')
            $arrData['oum_depend'] = 'Sq.ft.';

        //sell_price
        if(!isset($arrData['sell_by']))
            $arrData['sell_by'] = '';
        else if($arrData['sell_by']=='area' && isset($arrData['oum']) && isset($lenghKey[$arrData['oum']]))
            $defaultUnitLength = $lenghKey[$arrData['oum']];
        else if($arrData['sell_by']=='lengths' && isset($arrData['oum'])){
            if(!isset($permeterKey[$arrData['oum']]))
                $arrData['oum'] = 'Lr. ft.';
            $defaultUnitLength = $permeterKey[$arrData['oum']];
        }

        //sell_price
        if(isset($arrData['sell_price']))
            $arrData['sell_price'] = (float)$arrData['sell_price'];
        else
            $arrData['sell_price'] = 0;
        return $arrData;
    }

    public static function calBleed(&$arrData, $callFromOutside = false)
    {
        if( $callFromOutside )
            $arrData = self::setDefault($arrData);
        if(( $callFromOutside || (!isset($arrData['same_parent']) || !$arrData['same_parent']) )
            && isset($arrData['sizew']) && isset($arrData['sizeh']) ) {
            $productBleed['pricing_bleed'] = 0;
            $productBleed = self::findFirst([
                                [
                                    'deleted'  => false,
                                    '_id'      => new \MongoId($arrData['_id'])
                                ],
                                'fields' => ['pricing_bleed']
                            ]);
            if(gettype($productBleed)=="array"){
                    if( isset($productBleed['pricing_bleed'])  && is_numeric($productBleed['pricing_bleed']) ) {
                        $unitConverter = new \RW\UnitConvertor\MyUnitConverter;
                        $productBleed = $productBleed['pricing_bleed'];
                        $bleed = JTStuff::findFirst([
                                        [
                                            'value'      => 'bleed_type'
                                        ],
                                        'fields' => ['option']
                                    ]);
                        if( isset($bleed['option']) ) {
                            $bleed = array_filter($bleed['option'], function($arrData) use($productBleed){
                                return $arrData['key'] == $productBleed;
                            });
                            if( empty($bleed) )
                                return array();
                            $bleed = reset($bleed);
                            if( !isset($arrData['sizew_unit']) || empty($arrData['sizew_unit']) )
                                $arrData['sizew_unit'] = 'in'; //unit default
                            if( !isset($arrData['sizeh_unit']) || empty($arrData['sizeh_unit']) )
                                $arrData['sizeh_unit'] = 'in';//unit default
                            $bleedw = (float)str_replace(",","",$unitConverter->myConvert($bleed['sizew'],$bleed['sizew_unit'],$arrData['sizew_unit'],5));
                            $bleedh = (float)str_replace(",","",$unitConverter->myConvert($bleed['sizeh'],$bleed['sizeh_unit'],$arrData['sizeh_unit'],5));
                            $arr_return = array();
                            if( $callFromOutside ) {
                                $arr_return = array(
                                        'bleed_sizew' => $bleedw,
                                        'bleed_sizeh' => $bleedh,
                                    );
                            } else if($arrData['sell_by'] != 'unit') {
                                $sizew = str_replace(",","",$unitConverter->myConvert($arrData['sizew'],$arrData['sizew_unit'],'ft',5));
                                $sizeh = str_replace(",","",$unitConverter->myConvert($arrData['sizeh'],$arrData['sizeh_unit'],'ft',5));
                                $area = ($sizew + $bleedw) * ($sizeh + $bleedh);
                                $perimeter = (($sizew + $bleedw) + ($sizeh + $bleedh)) / 2;
                                $oldAdjQty = $arrData['adj_qty'];
                                $oldArea = $arrData['area'];
                                $oldPerimeter= $arrData['perimeter'];
                                $arrData['area'] = $area;
                                $arrData['perimeter'] = $perimeter;
                                $arrData['adj_qty'] = (float)$arrData['quantity']* $area;
                                $arrData['bleed'] = true;
                                $arr_return = array('adj_qty' => $oldAdjQty, 'area' => $oldArea, 'perimeter' => $oldPerimeter);
                            }
                            return $arr_return;
                        }
                    }
            }
                    
        }
        $arrData['bleed'] = false;
        return array();
    }

    public static function calArea(&$arrData, $unitConverter)
    {
        if(isset($arrData['sizew']) && isset($arrData['sizeh']) && (float)$arrData['sizew']>0 && (float)$arrData['sizeh']>0){
            if(!isset($arrData['sizew_unit']) || $arrData['sizew_unit']=='')
                $arrData['sizew_unit'] = 'in'; //unit default
            if(!isset($arrData['sizeh_unit']) || $arrData['sizeh_unit']=='')
                $arrData['sizeh_unit'] = 'in';//unit default
            $sizew = (float)$arrData['sizew'];
            $sizeh = (float)$arrData['sizeh'];
            $sizew = str_replace(",","",$unitConverter->myConvert($sizew,$arrData['sizew_unit'],'ft',5));
            $sizeh = str_replace(",","",$unitConverter->myConvert($sizeh,$arrData['sizeh_unit'],'ft',5));
            $arrData['area'] =  (float)$sizew * (float)$sizeh;
        }else if(isset($arrData['sell_by']) && $arrData['sell_by']=='unit'){
            $arrData['area'] = 1;
        }else{
            $arrData['area'] = 0;
        }
    }

    //tính chu vi
    public static function calPerimeter(&$arrData, $unitConverter)
    {
        if(isset($arrData['sizew']) && isset($arrData['sizeh']) && (float)$arrData['sizew']>0 && (float)$arrData['sizeh']>0){
            if(!isset($arrData['sizew_unit']) || $arrData['sizew_unit']=='')
                $arrData['sizew_unit'] = 'in'; //unit default
            if(!isset($arrData['sizeh_unit']) || $arrData['sizeh_unit']=='')
                $arrData['sizeh_unit'] = 'in';//unit default
            $sizew = (float)$arrData['sizew'];
            $sizeh = (float)$arrData['sizeh'];
            $sizew = $unitConverter->myConvert($sizew,$arrData['sizew_unit'],'ft',5);
            $sizeh = $unitConverter->myConvert($sizeh,$arrData['sizeh_unit'],'ft',5);
            $arrData['perimeter'] =  2*((float)$sizew + (float)$sizeh);

        }else if(isset($arrData['sell_by']) && $arrData['sell_by']=='unit'){
            $arrData['perimeter'] = 1;
        }else{
            $arrData['perimeter'] = 0;
        }
    }

    public static function calAdjQty(&$arrData)
    {
        if(isset($arrData['sell_by']) && strtolower($arrData['sell_by'])=='area'){
            $arrData['adj_qty'] = (float)$arrData['quantity']*(float)$arrData['area'];
        }else if(isset($arrData['sell_by']) && strtolower($arrData['sell_by'])=='lengths'){
            $arrData['adj_qty'] = (float)$arrData['quantity']*(float)$arrData['perimeter'];
        }else{
            $arrData['adj_qty'] = (float)$arrData['quantity'];
        }
    }

    public static function calUnitPrice(&$arrData)
    {
        if($arrData['sell_by']=='unit' || $arrData['sell_by']=='Unit')
            $arrData['unit_price'] = (float)$arrData['sell_price'];
        else if($arrData['sell_by']=='lengths' && $arrData['sell_price']!='' && isset($arrData['perimeter']))
            $arrData['unit_price'] = (float)$arrData['sell_price']*(float)$arrData['perimeter'];
        else if($arrData['sell_by']=='area' && $arrData['sell_price']!='' && isset($arrData['area']))
            $arrData['unit_price'] = (float)$arrData['sell_price']*(float)$arrData['area'];
        else if($arrData['sell_by']=='combination')
            $arrData['unit_price'] = (float)$arrData['sell_price']*(float)$arrData['area'];
        else
            $arrData['unit_price'] = 0;
    }

    public static function calSubTotal(&$arrData)
    {
        //update lai cach tinh gia trong JT
        $adjustment = isset($arrData['adjustment'])?(float)$arrData['adjustment']:0;
        if(isset($arrData['unit_price']) && $arrData['unit_price']!='' && isset($arrData['quantity']))
            $arrData['sub_total'] = round(($adjustment+(float)$arrData['unit_price'])*(float)$arrData['quantity'],3);
        else
            $arrData['sub_total'] = 0;
    }

    public static function calTax(&$arrData)
    {
        if(isset($arrData['use_tax']) && isset($arrData['taxper']) && is_numeric($arrData['taxper']) && isset($arrData['sub_total']))
            $arrData['tax'] = round(((float)$arrData['taxper']/100)*(float)$arrData['sub_total'],3);
        else
        // Tax cộng vào sau
            $arrData['tax'] = 0;
    }

    public static function calAmount(&$arrData)
    {
        $arr = $arrData;
        if(isset($arrData['sub_total']) && isset($arrData['tax']))
            $arrData['amount'] = round((float)$arrData['sub_total']+(float)$arrData['tax'],3);
    }

    public static function discount(&$arrData)
    {
        if(isset($arrData['price_break_from_to']['discount']))
            $arrData['sell_price'] = (1-((float)$arrData['price_break_from_to']['discount']/100))*$arrData['sell_price'];
    }

    public static function netDiscount(&$sum, $discount)
    {
        $discountPrice = 0;
        if( $discount ) {
            $discount = (float)$discount;
            $discountPrice = round(( $sum * $discount ) / 100, 3);
            $sum -= $discountPrice;
        }
        return $discountPrice;
    }

    public static function calPrice(&$arrData, $calPriceBreak = true)
    {
        $unitConverter = new \RW\UnitConvertor\MyUnitConverter;
        $arrData = self::setDefault($arrData);
        $innerBleed = false;
        if( isset($arrData['bleed_sizew']) && isset($arrData['bleed_sizeh']) ) {
            $innerBleed = true;
            $arrData['sizew'] += $arrData['bleed_sizew'];
            $arrData['sizeh'] += $arrData['bleed_sizeh'];
        }
        self::calArea($arrData, $unitConverter);
        self::calPerimeter($arrData, $unitConverter);
        self::calAdjQty($arrData);
        $bleed = [];
        if( !$innerBleed ) {
            $bleed = self::calBleed($arrData);
        }
        if ($calPriceBreak) {
            self::calPriceBreak($arrData);
        }
        self::calUnitPrice($arrData);
        if( isset($arrData['plus_sell_price']) ){
            $arrData['sell_price'] = $arrData['unit_price'];
            $arrData['sell_price'] += $arrData['plus_sell_price'];
            $arrData['unit_price'] = $arrData['sell_price'];
        }
        if( isset($arrData['sell_by']) && $arrData['sell_by'] == 'area'
                && isset($arrData['pricing_method']) && $arrData['pricing_method'] == 'small_area'  )
            self::calSmallArea($arrData);
        self::calSubTotal($arrData);
        if( !empty($bleed) ) {
            $arrData['adj_qty'] = $bleed['adj_qty'];
            $arrData['area'] = $bleed['area'];
            $arrData['perimeter'] = $bleed['perimeter'];
        } else if( $innerBleed ) {
            $arrData['sizew'] -= $arrData['bleed_sizew'];
            $arrData['sizeh'] -= $arrData['bleed_sizeh'];
            $arrData['bleed'] = true;
            self::calArea($arrData, $unitConverter);
            self::calPerimeter($arrData, $unitConverter);
            self::calAdjQty($arrData);
        }
    }

    public static function getPrice($data)
    {
        $arrReturn = ['sell_price'=> 0, 'sub_total'=> 0];
        if( !isset($data['_id']) || strlen($data['_id']) != 24 ) {
            return $arrReturn;
        }

        $sizeW = isset($data['sizew']) ? $data['sizew'] : '';
        $sizeH = isset($data['sizeh']) ? $data['sizeh'] : '';
        $quantity   = isset($data['quantity']) ? $data['quantity'] : '';
        $company_id = isset($data['companyId']) ? $data['companyId'] : '';
        $options    = isset($data['options']) ? $data['options'] : [] ;

        $product = self::findFirst([
                [
                    'deleted'       => false,
                    '_id'           => new \MongoId( $data['_id'] ),
                    'assemply_item' => 1,
                ],
                'fields' => ['_id', 'name', 'description', 'sell_by', 'sell_price','options', 'pricebreaks', 'sellprices', 'pricing_method','gst_tax','category']
            ]);
        if( is_object($product) ) {
            $product = returnArray($product);//$product->toArray();
            $product['sizew'] = $sizeW;
            $product['sizeh'] = $sizeH;
            $product['sizew_unit'] = $product['sizeh_unit'] = 'in';
            $product['quantity'] = $quantity;
            $company = [];
            if( !empty($company_id) && strlen($company_id) == 24 ){
                $company = JTCompany::findFirst([
                    [
                        '_id'       => new \MongoId($company_id),
                        'deleted'   => false,
                    ],
                    'fields' => ['sell_category','sell_category_id','pricing','discount', 'net_discount']
                ]);
                if( is_object($company) )
                    $company = $company->toArray();
                else
                    $company = [];
            }
            $product['price_break'] = self::priceBreak($product, $company);
            if( !isset($product['options']) ) {
                $product['options'] = [];
            }
            $arrOptions = [];
            foreach($options as $option){
                if ($option['quantity'] == 0) continue;
                $option_id = $option['_id'];
                foreach($product['options'] as $opt_k => $opt){
                    if( isset($opt['deleted']) && $opt['deleted'] || !is_object($opt['product_id']) ){
                        unset($product['options'][$opt_k]); continue;
                    }
                    if( (string)$opt['product_id'] == $option_id ){
                        $tmpOpt = self::findFirst([
                                [
                                    'deleted'       => false,
                                    '_id'           => new \MongoId( $opt['product_id'] ),
                                ],
                                'fields' => ['name', 'sell_price','sell_by', 'pricebreaks', 'sellprices', 'pricing_method']
                            ]);
                        if( is_object($tmpOpt) ){
                            $tmpOpt = $tmpOpt->toArray();
                            $opt = array_merge($opt, $tmpOpt);
                        }
                        $opt = array_merge($opt, $option);
                        if( !isset($opt['same_parent']) || !$opt['same_parent'] ){
                            $opt['same_parent'] = 0;
                            $opt['quantity'] = isset($opt['quantity']) && $opt['quantity'] ? $opt['quantity'] : 1;
                        } else {
                            $opt['quantity'] = isset($opt['quantity']) && $opt['quantity'] ? $opt['quantity'] : 1;
                        }
                        $opt['require'] = isset( $opt['require'] ) ? $opt['require'] : 0;
                        //get file_qty for Digital File Process and Print
                        $opt['choose'] = 1;
                        $arrOptions[] = $opt;
                        unset($product['options'][$opt_k]);
                        break;
                    }
                }
            }
            $plusSellPrice = $totalOtherLine = 0;
            //=============Cal-Bleed=============
            $lineBleed = self::calBleed($product, true);
            if( !empty($lineBleed) ){
                $product['bleed_sizew'] = $lineBleed['bleed_sizew'];
                $product['bleed_sizeh'] = $lineBleed['bleed_sizeh'];
            } else {
                $product['bleed_sizew'] = $product['bleed_sizeh'] = 0;
            }
            //=============End Cal-Bleed=========
            //=============Loop option bleed=============
            foreach($arrOptions as $option) {
                if(isset($option['same_parent'])&&$option['same_parent']
                    && $option['choose']){
                        $option['sizew'] = $sizeW;
                        $option['sizeh'] = $sizeH;
                        $option['sizew_unit'] = $option['sizeh_unit'] = 'in';
                        $optionBleed = self::calBleed($option, true);
                        if( !empty($optionBleed) ) {
                            $product['bleed_sizew'] += $optionBleed['bleed_sizew'];
                            $product['bleed_sizeh'] += $optionBleed['bleed_sizeh'];
                        }
                }
            }
            //=============Loop option bleed=========
            //=============Check bleed=============
            if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
            if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
            //=============End Check bleed=========
            foreach($arrOptions as $option) {
                if( isset($option['same_parent']) && $option['same_parent'] ){
                    $option['sizew'] = $sizeW;
                    $option['sizeh'] = $sizeH;
                    $option['sizew_unit'] = $option['sizeh_unit'] = 'in';
                    if( isset($product['bleed_sizew']) ) {
                        $option['bleed_sizew'] = $product['bleed_sizew'];
                    }
                    if( isset($product['bleed_sizeh']) ) {
                        $option['bleed_sizeh'] = $product['bleed_sizeh'];
                    }
                    $tmp = $option;
                    $tmp['price_break'] = self::priceBreak($tmp, $company);
                    $tmp['quantity'] *= $quantity;
                    self::calPrice($tmp);
                    $option['sell_price'] = $tmp['sell_price'];
                    unset($tmp);
                } else {
                    $option['price_break'] = self::priceBreak($option, $company);
                }
                self::calPrice($option);

                if( !$option['choose'] ) continue;
                if( $option['same_parent'] ) {
                    $plusSellPrice += $option['sub_total'];//S.P thi cong don de nhan qty line chinh
                } else {
                    $totalOtherLine += $option['sub_total'];//khong phai SP thi tinh rieng va total lai
                }
            }
            //=============Check bleed=============
            if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
            if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
            //=============End Check bleed=========
            $product['plus_sell_price'] = $plusSellPrice;
            self::calPrice($product, 1);
            if( isset($company['net_discount']) ){
                self::netDiscount($product['sub_total'], $company['net_discount']);
                self::netDiscount($product['sell_price'], $company['net_discount']);
                $product['unit_price'] = $product['sell_price'];
            }
            if( $quantity )
                $product['sell_price'] += $totalOtherLine/$quantity;
            $product['sub_total'] += $totalOtherLine;
            if( !$quantity )
                $product['sell_price'] = $product['sub_total'] = 0;
            $arrReturn = [
                        'sell_price'    => $product['sell_price'],
                        'sub_total'     => $product['sub_total'],
                    ];
            if(isset($product['gst_tax']) && !empty($product['gst_tax']))
                $product['taxper'] = JTTax::getTaxper($product['gst_tax']);
            else
                $product['taxper'] = 5;
        }

        if( isset($data['fields']) ) {
            foreach($data['fields'] as $field) {
                $arrReturn[ $field ] = isset($product[$field]) ? $product[$field] : '';
            }
        }
        return $arrReturn;
    }


    public static function FilterOptions($options,$id){
        $default_option = $option_type = array();
        $jtproduct = self::findFirst(array(
            'conditions'=>array('_id'=>$id)
        ));
        if(isset($jtproduct->options)){
            foreach ($jtproduct->options as $key => $value){
                if($value['deleted']==false){
                    if(!isset($value['require']) || $value['require']==0)
                        $value['quantity'] = 0;                 
                    if(isset($value['finish']))
                        $value['quantity'] = (int)$value['finish'];
                    $default_option[(string)$value['product_id']] = $value['quantity'];
                    if(!isset($value['option_group']) || $value['option_group']==''){
                        $value['option_group'] = 'null';
                    }
                    if(isset($value['group_type']) && $value['group_type']=='Exc' && $value['require']==1)
                        $exc_option_default[GroupToKey($value['option_group'])] = (string)$value['product_id'];
                }
            }
        }
        // pr($exc_option_default);
        foreach ($options as $key => $value) {
            if(isset($value['isfinish']) && $value['isfinish']==1)
                $options[$key]['finish'] = $value['quantity'];
            else
                $options[$key]['finish'] = '';

            if(isset($exc_option_default[$value['group_id']])){ //Exc
                if($value['quantity']!=$default_option[$value['_id']] && $value['_id']!=$exc_option_default[$value['group_id']])
                    $options[$key]['is_change'] = 1;
                else
                    $options[$key]['is_change'] = 0;
            }else if(isset($value['default']) && $value['default']==1){//case drink option
                if($value['quantity']==1)
                    $options[$key]['is_change'] = 1;
                else
                    $options[$key]['is_change'] = 0; 

            }else if(isset($default_option[$value['_id']]) && $value['quantity']!=$default_option[$value['_id']]){
                $options[$key]['is_change'] = 1;
            }else{
                $options[$key]['is_change'] = 0;
            }
        }
        // pr($options);die;
        return $options;
    }

    public static function findByAssetType($asset_type='bms'){ //bms,drink,kitchen
        $arr_sync = array(
                        'bms'=>'51ef8224222aad6011000092',
                        'drink'=>'5279a88767b96daa4b000029',
                        'kitchen'=>'52b738cbe6f2b23a680943c3',
                        'online'=>'52b738cbe6f2b23a680943c3',
                        'manager' => '52b73fa3e6f2b24e690943b2'
                    );
        $query = self::find([
            'conditions'  => [
                'deleted'           => false,
                'product_type'      => 'Product',
                'production_step'   => array(
                                            '$elemMatch' => array(
                                                                'tag_key'=>new \MongoId($arr_sync[$asset_type]),
                                                                'deleted'=>false
                                                            )
                                        )
            ],
            'sort'      => ['code' => -1]
        ]);
        $arr_bms = $arr_bms_pro = array();
        foreach ($query as $key => $value) {
            $arr_bms[] = $value->_id;
            $arr_bms_pro[(string)$value->_id] = $value->toArray();
        }
        return array(
                'arr_bms'=>$arr_bms,
                'arr_bms_pro'=>$arr_bms_pro
                );
    }
}
