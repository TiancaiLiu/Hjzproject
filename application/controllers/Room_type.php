<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/6
 * Time: 下午2:17
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_type extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    /**
     *  分页获取所有公寓列表(后端接口)
     */
    public function get_apartment_list()
    {
        $page_index = isset($_GET['page_index']) ? intval($_GET['page_index']) : 1;
        $page_size = isset($_GET['page_size']) ? intval($_GET['page_size']) : 10;
        $status = isset($_GET['status']) ? intval($_GET['status']) : NULL;

        if($page_index < 1 || $page_size <= 0) {
            throw new Exception("参数异常", -1);
        }
        $params = array("status"=>$status, "page_index"=>$page_index, "page_size"=>$page_size);
        $data = $this->Room_type_model->get_batch_apartment($params);
        if (empty($data)) {
            $return_data = array(
                'status' => -1,
                'msg'    => '暂无数据'
            );
            echo json_encode($return_data);
            exit();
        } else {
            $return_data = array(
                'status'  => 0,
                'msg'     => '成功',
                'data'    => $data
            );
            echo json_encode($return_data);
            exit();
        }
    }

}