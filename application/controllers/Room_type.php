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
        $this->load->model('Room_type_model');
    }

    /**
     *  分页获取所有公寓列表(后端接口)
     */
    public function get_room_type_list()
    {
        $page_index = isset($_GET['page_index']) ? intval($_GET['page_index']) : 1;
        $page_size = isset($_GET['page_size']) ? intval($_GET['page_size']) : 10;
        $status = isset($_GET['status']) ? intval($_GET['status']) : NULL;

        if($page_index < 1 || $page_size <= 0) {
            throw new Exception("参数异常", -1);
        }
        $params = array("status"=>$status, "page_index"=>$page_index, "page_size"=>$page_size);
        $data = $this->Room_type_model->get_batch_room_type($params);
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

    /**
     * ##更新房型状态状态##
     * @throws Exception
     */
    public function update_room_type_status() {
        requires_input(array('c_room_type_id', 'status'));
        $room_type_data = get_json_format_data();
        $room_type_id = intval($room_type_data['c_room_type_id']);
        $status = intval($room_type_data['status']);

        if($room_type_id <= 0 || !in_array($status, array(0, 1))) {
            throw new Exception("参数异常", -10);
        }
        $ret = $this->Common_model->update('readdb', 't_room_type_0', array("c_status" => $status), array("c_room_type_id" => $room_type_id));
        if($ret) {
            $return_data = array(
                'status'    => 0,
                'msg'       => '成功'
            );
            echo json_encode($return_data);
            exit();
        } else {
            throw new Exception("数据库异常", -10);
        }
    }

    /**
     *  根据城市id返回所有公寓房型列表(小程序)
     */
    public function get_list()
    {
        $input = get_json_format_data();
        requires_input(array('city_id'));
        $city_id = $input['city_id'];

        $apartment_room_info = $this->Room_type_model->wx_get_room_type_list(2001);

    }

}