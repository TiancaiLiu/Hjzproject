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
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    private function get_apartment_list()
    {
        $data = $this->Common_model->get('readdb','t_apartment_0', '*',array('c_is_online_apartment'=>1, 'c_is_test'=>0));
        return empty($data) ? array() : $data;
    }

    /**
     * 分页获取公寓列表
     * @return array
     */
    public function get_batch_apartment($params)
    {
        $page_size = $params['page_size'];
        $page_index = $params['page_index'];
        $status = $params['status'];
        $name = $params['name'];

        $ret['page_index'] = $params['page_index'];
        $ret['page_size'] = $params['page_size'];

        $offset = ($page_index-1) * $page_size;
        $limit = $page_size;

        if($status == NULL) {
            $this->readdb->where(array('c_status !=' => 9));
        } else {
            $this->readdb->where(array('c_status' => $status));
        }

        // 根据公寓名搜索
        if (!empty($name)) {
            $this->readdb->like('c_name', $name);
        }
        $apartment_info = $this->get_apartment_list();
        $apartment_ids = array_column($apartment_info, "c_apartment_id");
        if(!empty($apartment_ids)) {
            $this->readdb->where_in('c_apartment_id', $apartment_ids);
            $ret['row_count'] = $this->readdb->count_all_results('t_apartment_0', FALSE);
            $ret['page_count'] = ceil($ret['row_count'] / $params['page_size']);
            $ret['list'] = trans_db_result($this->readdb
                ->limit($params['page_size'], $params['page_size'] * ($params['page_index'] - 1))
                ->order_by("c_apartment_id desc")
                ->get()
                ->result_array());
            foreach($ret['list'] as &$apartment_info) {
                $apartment_info['weekend'] = str_to_array($apartment_info['weekend']);
                $apartment_info['amenity_str'] = str_to_array($apartment_info['amenity_str']);
                $apartment_info['tag_str'] = str_to_array($apartment_info['tag_str']);
                unset($apartment_info['intro']);
            }
        } else {
            $ret['row_count'] = 0;
            $ret['page_count'] = 0;
            $ret['list'] = array();
        }
        return $ret;
    }

    /**
     * ##获取公寓设施##
     * @param unknown $dic_type
     * @param unknown $limit
     * @param unknown $offset
     * @return multitype:
     */
    public function get_dictionary_list()
    {
        $sql = "select c_dictionary_id,c_number,c_name,c_type,c_icon,c_create_time from t_dictionary_0 where c_type=3 and c_status=0 order by c_create_time desc";
        $query = $this->readdb->query($sql);
        $ret = $query ? $query->result_array() : array();
        return trans_db_result($ret);
    }

}