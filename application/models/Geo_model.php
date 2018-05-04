<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/4
 * Time: 下午7:06
 */

if (! defined('BASEPATH')) exit('No direct script access allowed');

class Geo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb', TRUE);
    }

    /**
     * 获取城市分组
     * @return array 城市分组
     */
    public function get_city_group($filter_map_type = false)
    {
        $query = $this->readdb->query("
            SELECT DISTINCT apartment.c_city_id
            FROM t_apartment_0 AS apartment
            JOIN t_room_type_0 AS room_type ON room_type.c_apartment_id = apartment.c_apartment_id
            WHERE apartment.c_status = 0 AND apartment.c_is_test = 0
                AND room_type.c_status = 0 AND room_type.c_is_online = 1
        ");

        $city_ids = trans_db_result($query->result_array());

        if (empty($city_ids)) {
            return array();
        }

        $city_ids = array_column($city_ids, "city_id");

        $sql = "
            SELECT
                province.c_province_id,
                province.c_name AS province_name,
                province.c_pinyin_short AS province_pinyin_short,
                province.c_longitude AS province_longitude,
                province.c_latitude AS province_latitude,
                city.c_city_id, city.c_name AS city_name,
                city.c_pinyin_short AS city_pinyin_short,
                city.c_longitude AS city_longitude,
                city.c_latitude AS city_latitude,
                city.c_map_type AS city_map_type
            FROM t_city_0 as city
            JOIN t_province_0 AS province ON province.c_province_id = city.c_province_id
            WHERE city.c_city_id IN ?
        " . ($filter_map_type ? " AND city.c_map_type = 1" : "");

        $query = $this->readdb->query($sql, array($city_ids));

        return trans_db_result($query->result_array());
    }
}