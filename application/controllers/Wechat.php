<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/6
 * Time: 下午1:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    /**
     *  微信登录获取openid session_key
     */
    public function wx_login()
    {
        $config = config_item('wxSmallPayConfig');
        requires_input(array('code'));
        $frm = get_json_format_data();
        $code_api = "https://api.weixin.qq.com/sns/jscode2session?appid=". $config['APPID'] ."&secret=". $config['APPSECRET'] ."&js_code=". $frm['code'] ."&grant_type=authorization_code";
        $ret_arr = curl_get($code_api);

        if (empty($ret_arr['openid']) || empty($ret_arr['session_key'])) {
            throw new Exception($ret_arr['errmsg']);
        }

        $openid = $ret_arr['openid'];

        $user_data = array(
            'openid'    => $openid,
            'ctime'     => time()
        );

        $this->readdb->insert('user', $user_data);
        $userId = $this->readdb->insert_id();
        if (!$userId) {
            $return_data = array(
                'status'    => -1,
                'msg'       => '用户绑定失败'
            );
            echo json_encode($return_data);
            exit();
        }
        $user_info_data = array(
            'userId'    => $userId,
            'level'     => 1,
            'ctime'     => time(),
            'registTime'=> time()
        );
        $ret = $this->readdb->insert('user_info', $user_info_data);
        if ($ret) {
            $return_data = array(
                'status'   => 0,
                'msg'      => '用户绑定成功',
                'openid'  => $openid,
            );
            echo json_encode($return_data);
            exit();
        }


    }
}