<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2018/5/4
 * Time: 下午7:04
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Geo extends CI_Controller
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
     *  获取所有公寓列表(后端接口)
     */
    public function get_apartment_list()
    {
        //读数据内容，打包吐给前端
        $apartment_info = $this->Apartment_model->get_apartment_list();
        if (!empty($apartment_info)) {
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
                'data'    => $apartment_info
            );
            echo json_encode($return_data);
            exit();
        }
    }
}