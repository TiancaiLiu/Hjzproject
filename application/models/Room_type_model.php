<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/6
 * Time: 下午2:20
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Room_type_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    private function get_room_type_list()
    {
        $data = $this->Common_model->get('readdb','t_room_type_0', '*',array('c_status'=>0, 'c_is_online'=>1));
        return empty($data) ? array() : $data;
    }

    /**
     * 分页获取房型列表
     * @return array
     */
    public function get_batch_room_type($params)
    {
        $page_size = $params['page_size'];
        $page_index = $params['page_index'];
        $status = $params['status'];

        $ret['page_index'] = $params['page_index'];
        $ret['page_size'] = $params['page_size'];

        $offset = ($page_index-1) * $page_size;
        $limit = $page_size;

        if($status == NULL) {
            $this->readdb->where(array('c_status !=' => 9));
        } else {
            $this->readdb->where(array('c_status' => $status));
        }

        $room_type_info = $this->get_room_type_list();
        $room_type_ids = array_column($room_type_info, "c_room_type_id");
        if(!empty($room_type_ids)) {
            $this->readdb->where_in('c_room_type_id', $room_type_ids);
            $ret['row_count'] = $this->readdb->count_all_results('t_room_type_0', FALSE);
            $ret['page_count'] = ceil($ret['row_count'] / $params['page_size']);
            $ret['list'] = trans_db_result($this->readdb
                ->limit($params['page_size'], $params['page_size'] * ($params['page_index'] - 1))
                ->order_by("c_room_type_id desc")
                ->get()
                ->result_array());
            foreach ($ret['list'] as &$room_type_info) {
                $room_type_info['apartment_name'] = $this->Common_model->get_one('readdb', 't_apartment_0', 'c_name', array('c_apartment_id'=>$room_type_info['apartment_id'])['c_name']);
                unset($room_type_info['apartment_id']);
            }
        } else {
            $ret['row_count'] = 0;
            $ret['page_count'] = 0;
            $ret['list'] = array();
        }
        return $ret;
    }
}