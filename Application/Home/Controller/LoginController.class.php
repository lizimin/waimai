<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller{	
	public function login() {
		header("Content-Type:text/html; charset=utf-8");
		$User = M('user');
		$result = $User -> where($_POST) -> select();
		if ($result) {
			session('frontendStatus', 'true');
			session('user_id', $result[0]['id']);
			session('brand_id', $result[0]['brand_id']);
			session('account', $result[0]['username']);
			$this -> ajaxReturn(array('msg' => 'success'));
		}else {
			$this -> ajaxReturn(array(
					'msg' => 'fail',
					'info' => '账号密码有误'
			));
		}
	}
	public function pageLogin(){
		$this->display('Admin/Login/index');
	}
	public static function loginConfirm() {
		$loginStatus = session('frontendStatus');
		if ($loginStatus ==  'true') {
			return true;
		}else {
			return false;
		}
	}
	
	public function logout() {
		session('frontendStatus', null);
		session('user_id', null);
		session('brand_id', null);
	    session('account',null);
		if ($this -> loginConfirm()) {
			$this -> display("Admin/Index/index");
		}else {
			$this -> display("Admin/Login/index");
		}
	}
}