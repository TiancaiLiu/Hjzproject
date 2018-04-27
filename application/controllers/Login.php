<?php
/**
 * Created by PhpStorm.
 * User: liujingxiong
 * Content: 登录控制器
 * Date: 2018/4/27
 * Time: 下午5:30
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->readdb = $this->load->database('readdb', TRUE);
    }
}