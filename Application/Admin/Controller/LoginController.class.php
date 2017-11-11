<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{	
	public function login() {
		header("Content-Type:text/html; charset=utf-8");
		$User = D('Admin');
		$result = $User -> where($_POST) -> select();
		if ($result) {
			session('loginStatus', 'true');
			session('account', $result[0]['phone']);
			session('name', $result[0]['name']);
			session('level', $result[0]['level']);
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
		$loginStatus = session('loginStatus');
		if ($loginStatus ==  'true') {
			return true;
		}else {
			return false;
		}
	}
	
	public function logout() {
		session('loginStatus', null);
		session('account', null);
		session('name', null);
		if ($this -> loginConfirm()) {
			$this -> display("Admin/Index/index");
		}else {
			$this -> display("Admin/Login/index");
		}
	}
}