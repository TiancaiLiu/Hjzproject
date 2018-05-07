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
        $data = $this->Common_model->get('readdb','t_room_type_0', '*',array('c_is_online'=>1));
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
                $room_type_info['apartment_name'] = $this->Common_model->get_one('readdb', 't_apartment_0', 'c_name', array('c_apartment_id'=>$room_type_info['apartment_id']))['c_name'];
                unset($room_type_info['apartment_id']);
            }
        } else {
            $ret['row_count'] = 0;
            $ret['page_count'] = 0;
            $ret['list'] = array();
        }
        return $ret;
    }

    /**
     * 小程序获取公寓房型列表
     */
    public function wx_get_room_type_list($city_id)
    {
        $this->readdb->select('apartment.c_apartment_id,apartment.c_name as apartment_name,apartment_id.c_city_id,room_type.c_room_type_id,room_type.c_name as room_type_name,room_type.c_max_area,room_type.c_bedroom_count,room_type.c_bed_count,room_type.c_comment_avg_score,room_type.c_base_price');
        $this->readdb->from('t_room_type_0 as room_type');
        $this->readdb->join('t_apartment_0 as apartment', 'room_type.c_apartment_id=apartment.c_apartment_id', 'left');
        $this->readb->join('t_city_0 as city','apartment.c_city_id=city.c_city_id','left');
        $this->readdb->where('apartment.c_city_id', $city_id);
        $apartment_info = $this->readdb->get()->result_array();

        print_r($apartment_info);die;

    }
}