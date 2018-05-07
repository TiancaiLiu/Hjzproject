<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/6
 * Time: 下午2:20
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    private function get_coupon_list()
    {
        $data = $this->Common_model->get('readdb','t_coupon', '*');
        return empty($data) ? array() : $data;
    }

    /**
     * 分页获取优惠券列表
     * @return array
     */
    public function get_batch_coupon($params)
    {
        $page_size = $params['page_size'];
        $page_index = $params['page_index'];
        $status = $params['status'];

        $ret['page_index'] = $params['page_index'];
        $ret['page_size'] = $params['page_size'];

        $offset = ($page_index-1) * $page_size;
        $limit = $page_size;

        if($status == NULL) {
            $this->readdb->where(array('is_enable !=' => 9));
        } else {
            $this->readdb->where(array('is_enable' => $status));
        }

        $coupon_info = $this->get_coupon_list();
        $coupon_ids = array_column($coupon_info, "coupon_id");
        if(!empty($coupon_ids)) {
            $this->readdb->where_in('coupon_id', $coupon_ids);
            $ret['row_count'] = $this->readdb->count_all_results('t_coupon', FALSE);
            $ret['page_count'] = ceil($ret['row_count'] / $params['page_size']);
            $ret['list'] = trans_db_result($this->readdb
                ->limit($params['page_size'], $params['page_size'] * ($params['page_index'] - 1))
                ->order_by("coupon_id desc")
                ->get()
                ->result_array());
        } else {
            $ret['row_count'] = 0;
            $ret['page_count'] = 0;
            $ret['list'] = array();
        }
        return $ret;
    }
}