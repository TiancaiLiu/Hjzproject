<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Date: 2017/11/16
 * Time: 下午4:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->readdb = $this->load->database('readdb', TRUE);

        /*$rd_session = $this->input->post('rd_session');
        if(empty($rd_session)) {
            $rd_session = $this->input->get('rd_session');
        }
        if (empty($rd_session)) {
            $return_data = array(
                'status' => -10,
                'msg'    => '账号未登录'
            );
            echo json_encode($return_data);
            exit();
        }*/
    }
}