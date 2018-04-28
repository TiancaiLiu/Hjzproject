<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2017/11/17
 * Time: 上午10:49
 */
if (! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * ##新增记录##
     * @param 数据库配置名 $db
     * @param 表名 $table_name
     * @param 数据 $data
     */
    public function insert($db, $table_name, $data) {
        $this->$db->insert($table_name, $data);
        return $this->$db->insert_id();
    }

    public function insert_batch($db,$table_name,$data){
        $this->$db->insert_batch($table_name,$data);
    }

    /**
     * ##获取单条记录##
     * @param 数据库配置名 $db
     * @param 表名 $table_name
     * @param 检索字段 $select
     * @param 查询条件 $where
     * @return boolean
     */
    public function get_one($db, $table_name, $select="", $where = "") {
        if(!empty($where)) {
            $this->$db->where($where);
        }
        if(!empty($select)) {
            $this->$db->select($select);
        }
        $query = $this->$db->get($table_name);

        $ret = $query->num_rows() > 0 ? $query->row_array() : false;

        return $ret;
    }

    /**
     * ##更新记录##
     * @param 数据库配置名 $db
     * @param 表名 $table
     * @param 数据 $data
     * @param 条件 $where
     */
    public function update($db, $table_name, $data, $where) {
        $this->$db->where($where);
        return $this->$db->update($table_name, $data);
    }

    /**
     * ##获取数据##
     * @param 数据库配置名 $db
     * @param 表名 $table_name
     * @param 检索字段 $select
     * @param 查询条件 $where
     * @return multitype
     */
    public function get($db, $table_name, $select = "", $where = "") {
        if(!empty($where)) {
            $this->$db->where($where);
        }
        if(!empty($select)) {
            $this->$db->select($select);
        }
        if(!empty($order)) {
            $this->$db->order_by($order);
        }

        $query = $this->$db->get($table_name);

        $ret = $query->num_rows() > 0 ? $query->result_array() : array();

        return $ret;
    }
}