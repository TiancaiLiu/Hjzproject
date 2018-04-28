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
    if(empty($input_data)) {
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
