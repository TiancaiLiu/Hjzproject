<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/4
 * Time: 下午7:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Geo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Geo_model');
        $this->load->model('Apartment_model');
    }

    /**
     * 获取所有城市列表(前端调用接口)
     * @return array 城市列表
     */
    public function all_city_list()
    {
        $result = $this->get_province_list(true);
        $return_data = array(
            'status'=>0,
            'data'=>$result
        );
        echo json_encode($return_data);
        exit();
    }

    /**
     * 根据城市获取省份分组
     * @param  array $city_list 城市列表
     * @return array            省份列表
     */
    private function get_province_list($display_coordinate = false)
    {
        $city_list = $this->Geo_model->get_city_group();

        $province_list = array();

        //组合城市数据
        foreach ($city_list as $key => $city) {
            $province = !empty($province_list[$city["province_id"]]) ? $province_list[$city["province_id"]] : array(
                "id" => $city["province_id"],
                "name" => $city["province_name"],
                "pinyin_short" => $city["province_pinyin_short"],
                "city_list" => array()
            );

            $city_item = array(
                "id" => $city["city_id"],
                "name" => $city["city_name"],
                "pinyin_short" => $city["city_pinyin_short"],
                "map_type" => $city["city_map_type"]
            );

            if ($display_coordinate) {
                $province["longitude"] = $city["province_longitude"];
                $province["latitude"] = $city["province_latitude"];
                $city_item["longitude"] = $city["city_longitude"];
                $city_item["latitude"] = $city["city_latitude"];
            }

            array_push($province["city_list"], $city_item);

            $province_list[$city["province_id"]] = $province;
        }

        //排序省份
        uasort($province_list, function($p1, $p2){
            return strcmp($p1["pinyin_short"], $p2["pinyin_short"]);
        });

        //排序城市
        foreach ($province_list as $index => $province) {
            $city_list = $province["city_list"];

            uasort($city_list, function ($c1, $c2)
            {
                return strcmp($c1["pinyin_short"], $c2["pinyin_short"]);
            });

            $province["city_list"] = array_values($city_list);
            $province_list[$index] = $province;
        }

        return array_values($province_list);
    }

    /**
     *  分页获取所有公寓列表(后端接口)
     */
    public function get_apartment_list()
    {
        $page_index = isset($_GET['page_index']) ? intval($_GET['page_index']) : 1;
        $page_size = isset($_GET['page_size']) ? intval($_GET['page_size']) : 10;
        $status = isset($_GET['status']) ? intval($_GET['status']) : NULL;
        $name = isset($_GET['name']) ? $_GET['name'] : NULL; // 公寓名搜索

        if($page_index < 1 || $page_size <= 0) {
            throw new Exception("参数异常", -1);
        }
        $params = array("status"=>$status, "name"=>$name, "page_index"=>$page_index, "page_size"=>$page_size);
        $data = $this->Apartment_model->get_batch_apartment($params);
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
     *  新建公寓
     */
    public function new_apartment()
    {
        requires_input(array('name', 'contact_phone', 'contact_telephone', 'address', 'amenity_str', 'apartment_type', 'is_online_apartment'));
        $apartment_data = get_json_format_data();
        $apartment_info['c_is_test'] = isset($apartment_data['is_test']) ? intval($apartment_data['is_test']) : 0;
        $apartment_info['c_is_franchise'] = isset($apartment_data['is_franchise']) ? intval($apartment_data['is_franchise']) : 0;
        $apartment_info['c_apartment_type'] = $apartment_data['apartment_type'];
        $apartment_info['c_name'] = $apartment_data['name'];
        $apartment_info['c_contact_telephone'] = $apartment_data['contact_telephone'];
        $apartment_info['c_contact_phone'] = $apartment_data['contact_phone'];
        $apartment_info['c_contact_name'] = isset($apartment_data['contact_name']) ? $apartment_data['contact_name'] : '';
        $apartment_info['c_address'] = $apartment_data['address'];
        $apartment_info['c_amenity_str'] = $apartment_data['amenity_str'];
        $apartment_info['c_desc'] = isset($apartment_data['desc']) ? $apartment_data['desc'] : '';
        $apartment_info['c_tips'] = isset($apartment_data['tips']) ? $apartment_data['tips'] : '';
        $apartment_info['c_create_time'] = time();

        $this->readdb->insert('t_apartment_0', $apartment_info);
        $apartment_id = $this->readdb->insert_id();

        if ($apartment_id) {
            $return_data = array(
                'status' => 0,
                'msg'    => '成功',
                'data'   => array('apartment_id' => $apartment_id)
            );
            echo json_encode($return_data);
            exit();
        } else {
            $return_data = array(
                'status' => -1,
                'msg'    => '新建公寓失败'
            );
            echo json_encode($return_data);
            exit();
        }
    }

    public function get_apartment_amenity_list()
    {
        $list = $this->Apartment_model->get_dictionary_list();
        $return_data = array(
            'status'    => 0,
            'msg'       => 'success',
            'data'      => $list
        );
        echo json_encode($return_data);
        exit();
    }

    /**
     * 编辑公寓
     */
    public function update_apartment()
    {
        requires_input(array('apartment_id', 'name', 'contact_phone', 'contact_telephone', 'address', 'amenity_str', 'apartment_type', 'is_online_apartment'));
        $apartment_data = get_json_format_data();
        $apartment_id = $apartment_data['apartment_id'];
        $apartment_info['c_is_test'] = isset($apartment_data['is_test']) ? intval($apartment_data['is_test']) : 0;
        $apartment_info['c_is_franchise'] = isset($apartment_data['is_franchise']) ? intval($apartment_data['is_franchise']) : 0;
        $apartment_info['c_apartment_type'] = $apartment_data['apartment_type'];
        $apartment_info['c_name'] = $apartment_data['name'];
        $apartment_info['c_contact_telephone'] = $apartment_data['contact_telephone'];
        $apartment_info['c_contact_phone'] = $apartment_data['contact_phone'];
        $apartment_info['c_contact_name'] = isset($apartment_data['contact_name']) ? $apartment_data['contact_name'] : '';
        $apartment_info['c_address'] = $apartment_data['address'];
        $apartment_info['c_amenity_str'] = $apartment_data['amenity_str'];
        $apartment_info['c_desc'] = isset($apartment_data['desc']) ? $apartment_data['desc'] : '';
        $apartment_info['c_tips'] = isset($apartment_data['tips']) ? $apartment_data['tips'] : '';
        $apartment_info['c_country_id'] = isset($apartment_data['country_id']) ? $apartment_data['country_id'] : 1;
        $apartment_info['c_province_id'] = isset($apartment_data['province_id']) ? $apartment_data['country_id'] : 2000;
        $apartment_info['c_city_id'] = isset($apartment_data['city_id']) ? $apartment_data['city_id'] : 2003;
        $apartment_info['c_district_id'] = isset($apartment_data['district_id']) ? $apartment_data['c_district_id'] : 20030002;
        $apartment_info['c_create_time'] = time();

        $this->readdb->where('c_apartment_id', $apartment_id);
        $this->readdb->update('t_apartment_0', $apartment_info);

        if($this->readdb->affected_rows()) {
            $return_data = array(
                'status' => 0,
                'msg'    => '更新成功',
                'data'   => array('apartment_id' => $apartment_id)
            );
            echo json_encode($return_data);
            exit();
        } else {
            $return_data = array(
                'status' => -1,
                'msg'    => '更新公寓失败'
            );
            echo json_encode($return_data);
            exit();
        }
    }

    /**
     * 根据id获取公寓信息
     */
    public function get_apartment()
    {
        $apartment_id = isset($_GET['apartment_id']) ? intval($_GET['apartment_id']) : 0;
        if($apartment_id <= 0) {
            throw new Exception("参数异常", -10);
        }

        $apartment_info = $this->Common_model->get_one('readdb', 't_apartment_0', "*", array("c_apartment_id" => $apartment_id));
        echo json_encode($apartment_info);
        exit();
    }

    /**
     * ##更新公寓状态##
     * @throws Exception
     */
    public function update_apartment_status() {
        requires_input(array('apartment_id', 'status'));
        $apartment_data = get_json_format_data();
        $apartment_id = intval($apartment_data['apartment_id']);
        $status = intval($apartment_data['status']);

        if($apartment_id <= 0 || !in_array($status, array(0, 1))) {
            throw new Exception("参数异常", -10);
        }
        $ret = $this->Common_model->update('readdb', 't_apartment_0', array("c_status" => $status), array("c_apartment_id" => $apartment_id));
        if($ret) {
            $return_data = array(
                'status'    => 0,
                'msg'       => '禁用成功'
            );
            echo json_encode($return_data);
            exit();
        } else {
            throw new Exception("数据库异常", -10);
        }
    }
}
