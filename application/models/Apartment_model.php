<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/5
 * Time: 上午10:27
 */

class Apartment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取公寓列表
     * @return array
     */
    public function get_apartment_list()
    {
        $data = $this->Common_model->get('readdb', 't_apartment_0', '*', array('c_is_online_apartment'=>1,'c_is_test'=>0));
        return empty($data) ? array() : $data;
    }

}