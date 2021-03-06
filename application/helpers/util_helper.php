<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * content: 全局方法
 * Date: 2018/4/28
 * Time: 上午9:18
 */

/**
 * 获取json数据
 */
function get_json_format_data()
{
    $input_data = json_decode(trim(file_get_contents('php://input')), TRUE);
    if (empty($input_data)) {
        $url = strtolower($_SERVER['HTTP_HOST']);
        $CI = &get_instance();

        if ($url == 'www.hejizhen.cn') {
            $input_data = $CI->input->get();

            if (empty($input_data)) {
                $input_data = $CI->input->post();
            }
        }
    } else {
        return array();
    }

    return $input_data;
}

/**
 * 输入验证(只支持POST请求中传递JSON对象的情况)
 * @param array $keys 参数key数组
 * @throws Exception
 */
function requires_input($keys)
{
    if(!is_array($keys)){
        throw new Exception('第一个参数arr必须是数组！');
    }

    $frm = get_json_format_data();

    foreach ($keys as $index => $key){
        if(!isset($frm[$key]) || is_null($frm[$key]) || $frm[$key] === ''){
            echo json_encode(array('status'=>-10,'msg'=>'缺失必要参数:'.$key));
            exit();
        }
    }
}

/**
 * 数据库查询结果转换 (删除key的"c_"前缀)
 * @param 数揣 $data
 * @return unknown|multitype:unknown
 */
function trans_db_result($data)
{
    if(empty($data)) return $data;
    $ret = array();
    foreach ($data as $key => $value) {
        if(is_array($value))
        {
            $arr = trans_db_result($value);
            $tmp_key = strpos($key, 'c_') === 0 ? substr($key, strlen('c_')) : $key;
            $ret[$tmp_key] = $arr;
        } else {
            $tmp_key = strpos($key, 'c_') === 0 ? substr($key, strlen('c_')) : $key;
            $ret[$tmp_key] = $value;
        }
    }
    return $ret;
}

function str_to_array($str, $column = "") {
    if(empty($str)) {
        return array();
    }
    $ret_arr = json_decode($str, TRUE);
    if(!empty($column)) {
        return array_column($ret_arr, $column);
    }
    return $ret_arr;
}

function curl_get($url)
{
    $userId = empty($_COOKIE['userid']) ? null : intval($_COOKIE['userid']);
    $skey = empty($_COOKIE['skey']) ? null : urlencode($_COOKIE['skey']);
    $cookie = "userid=".$userId."; skey=".$skey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $contents = curl_exec($ch);
    $auth_data = json_decode($contents, true);
    curl_close($ch);
    return $auth_data;
}
