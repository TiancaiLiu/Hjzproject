<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/4/27
 * Time: 下午2:40
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->readdb->select('*')->from('t_adminer_0')->get()->result_array();
        print_r($data);
        echo '预订单列表';
    }
}
