<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Content: 登录控制器
 * Date: 2018/4/27
 * Time: 下午5:30
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->readdb = $this->load->database('readdb', TRUE);
        $this->load->model('Adminer_model');
    }

    /**
     *  后台登录
     */
    public function admin_login()
    {
        $frm = get_json_format_data();
        requires_input(array('userAccount', 'password'));
        $userAccount = $frm['userAccount'];
        $password = md5(trim($frm['password']));
        $user_data = $this->Adminer_model->get_adminer_by_account($userAccount);
        if (empty($user_data)) {
            $return_array = array(
                'status' => -1,
                'msg'    => '用户不存在',
            );
            echo json_encode($return_array);
            exit();
        } else {
            $user_data_passwd = $user_data['password']; //数据库密码
            if ($password !== $user_data_passwd) {
                $return_array = array(
                    'status' => -2,
                    'msg'    => '密码错误，请重新输入',
                );
                echo json_encode($return_array);
                exit();
            } else {
                $return_array = array(
                    'status' => 0,
                    'msg'    => '登录成功',
                    'rd_session'   => md5($userAccount),
                    'user'  => $userAccount,
                );
                echo json_encode($return_array);
                exit();
            }
        }

    }

    /*
     * 生成32位随机字符串
     */
    private function create_noncestr($length = 32, $str = "")
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *  退出登录
     */
    public function logout()
    {
        $return_array = array(
            'status' => 0,
            'msg'    => '退出成功',
        );
        echo json_encode($return_array);
        exit();
    }
}