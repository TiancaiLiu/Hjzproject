<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/4/28
 * Time: 上午9:27
 */
if (! defined('BASEPATH')) exit('No direct script access allowed');

class Adminer_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 通过账号查找用户信息
     * @param $account
     */
    public function get_adminer_by_account($account)
    {
        $user_info = $this->Common_model->get_one('readdb', 't_adminer_0', '*', array('userAccount'=>$account));
        return empty($user_info) ? array() : $user_info;
    }
}