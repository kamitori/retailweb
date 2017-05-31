<?php
//=================================================================================
defined('DS')               || define('DS', DIRECTORY_SEPARATOR);
defined('APP_PATH')         || define('APP_PATH', realpath('.'));
defined('PUBLIC_PATH')      || define('PUBLIC_PATH', APP_PATH . DS .'public');
defined('PHAMTOM_CONVERT')  || define('PHAMTOM_CONVERT', APP_PATH.DS.'phantomjs'.DS.'rasterize.js');
defined('THEME')            || define('THEME','themes/banhmisub/');
defined('THEMEPOS')         || define('THEMEPOS','/themes/vendhq/');
defined('THEMEPOSCASH')     || define('THEMEPOSCASH','themes/poscash/');
defined('THEMEKIO')         || define('THEMEKIO','themes/kiosk/');
//=================================================================================
$info = getInfo();
defined('DEBUG')    || define('DEBUG', $info['debug']);
defined('URL')      || define('URL', $info['url']);
defined('DB')    || define('DB', $info['db']);
defined('JT_DB')    || define('JT_DB', $info['jt_db']);
defined('JT_URL')    || define('JT_URL', $info['jt_url']);
defined('DEFAULT_CURRENCY')    || define('DEFAULT_CURRENCY', '$');
defined('_POS_PAGE_SIZE_')    || define('_POS_PAGE_SIZE_', 100);

//=================================================================================

function _checkIsset($data = '',$default=''){
    return (isset($data)?$data:$default);
}
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function display_format_currency($p_number,$number = 2){
    return DEFAULT_CURRENCY.number_format((double)$p_number,$number);
}
define('MONGO_VERION',1);
function returnArray($obj){
    if(MONGO_VERION) return (array)$obj;
    return $obj->toArray();
}
function rand_key($arr_item){
    $rand_keys = array_rand($arr_item, 1);    
    return $arr_item[$rand_keys];
}
function getInfo()
{
    $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    $arrInfo = [];
    $arrConfig = [
        'pos.banhmisub.com'  => [
                    'url'       => 'http://pos.banhmisub.com',
                    'db'        => 'banhmisub',
                    'jt_db'     => 'bms',
                    'jt_url'    => 'http://jt.banhmisub.com'
                ],
        'newpos.banhmisub.com'  => [
                    'url'       => 'http://newpos.banhmisub.com',
                    'db'        => 'newpos',
                    'jt_db'     => 'bms',
                    'jt_url'    => 'http://newjt.banhmisub.com'
                ],
        'pos.local'  => [
                    'url'       => 'http://pos.local',
                    'db'        => 'retailweb',
                    'jt_db'     => 'bms',
                    'jt_url'    => 'http://bms.local'
                ],
        'retailweb.com'=> [
                    'url'       => 'http://retailweb.com',
                    'db'        => 'retailweb',
                    'jt_db'     => 'bms',
                    'jt_url'    => 'http://bms.com'
                ],
    ];
    if (php_sapi_name() === 'cli') {
        if( DIRECTORY_SEPARATOR == '\\' ) {
            $arrInfo = $arrConfig['retailweb.com'];
        } else {
            $arrInfo = $arrConfig['pos.banhmisub.com'];
        }
    } else {
        $arrInfo = $arrConfig[$serverName];
    }
    if (in_array($serverName, ['retailweb', ''])) {
        $arrInfo['debug'] = true;
    } else {
        $arrInfo['debug'] = true;
    }
    return $arrInfo;
}

function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
//Display currency
function format_currency($num, $afterComma = -1){
    if(is_string($num))
        $num = str_replace(',', '', $num);
    $num = (float)$num;
    if($afterComma == -1)
        $afterComma = $_SESSION['format_currency'];
    $num = round($num,$afterComma);
    return number_format($num,$afterComma);
}

//Undisplay currency
function unformat_currency($str){
    $str = explode(".",$str);
    $dec = str_replace(",","",$str[0]);
    $dec = (int)$dec;
    if(isset($str[1]))
        $per = (int)$str[1];
    else
        $per = 0;
    $per = $per/1000;
    return $dec+$per;
}
function isImage($mime)
{
    return in_array($mime, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
}

function slug($string, $separator = '-')
{
    $title = \Patchwork\Utf8::toAscii($string);
    // Convert all dashes/underscores into separator
    $flip = $separator == '-' ? '_' : '-';

    $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

    return trim($title, $separator);
}


function create_random_key($p_len=10, $p_character_only = false,$number_only = false, $p_mixed_case=false){
    $v_chars = 'qwertyuiopasdfghjklzxcvbnm0123456789';
    $r='';
    if($p_character_only) $v_chars = preg_replace('/[^a-z]/', '', $v_chars);
    if($number_only) $v_chars = preg_replace('/[^0-9]/', '', $v_chars);
    $l = strlen($v_chars)-1;
    $check_total_key = 0;
    for($i=0;$i<$p_len;$i++){
        $p = rand(0,$l);
        $c = substr($v_chars,$p,1);
        if($p_mixed_case){
            $t = rand(0,1);
            $c = $t==1?strtoupper($c):$c;
        }           
        $r.= strtoupper($c);
    }
    return $r;
}

// Sort mảng theo giá trị key, hàm đơn giản
function aasort(&$array=array(), $key='',$order=1,$isResetKey = false) {
    $sorter=array();
    $ret=array();
    if(is_array($array) && count($array)>0){
        reset($array);
        foreach ($array as $ii => $va) {
            if(!isset($va[$key])) continue;
            $sorter[$ii]=$va[$key];
        }
    }
    if($order==1)
        asort($sorter);
    else
        arsort($sorter);
    if(!$isResetKey)
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
    else
        foreach ($sorter as $ii => $va) {
            $ret[]=$array[$ii];
        }
    $array=$ret;
    return $array;
}

// Sort mảng theo giá trị key, cho phép theo nhiều cách sort_flags
function msort($array, $key,$order=1,$sort_flags = SORT_REGULAR) {
    if (is_array($array) && count($array) > 0) {
        if (!empty($key)) {
            $mapping = array();
            foreach ($array as $k => $v) {
                $sort_key = '';
                if (!is_array($key)) {
                    $sort_key = $v[$key];
                } else {
                    // @TODO This should be fixed, now it will be sorted as string
                    foreach ($key as $key_key) {
                        $sort_key .= $v[$key_key];
                    }
                    $sort_flags = SORT_STRING;
                }
                $mapping[$k] = $sort_key;
            }
            if($order==1)
                asort($mapping, $sort_flags);
            else
                arsort($mapping, $sort_flags);
            $sorted = array();
            foreach ($mapping as $k => $v) {
                $sorted[] = $array[$k];
            }
            return $sorted;
        }
    }
    return $array;
}

function GroupToKey($str){
    $str = explode(".",$str);
    $str = end($str);
    $str = strtolower(str_replace(" ","_",trim($str)));
    return $str;
}
function GroupToName($str){
    $str = explode(".",$str);
    $str = end($str);
    return $str;
}

function time_elapsed_string($ptime){
    $etime = time() - $ptime;
    if ($etime < 1){
        return '0 seconds';
    }
    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );
    foreach ($a as $secs => $str){
        $d = $etime / $secs;
        if($d >= 1){
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}