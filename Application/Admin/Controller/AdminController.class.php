<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {
	public function __construct(){
		parent::__construct();
		if(LoginController::loginConfirm()){
			return true;
		}else{
			$this ->redirect('login/pageLogin');
		}
	}
    public function index(){
    	$this -> display("Admin/Index/index");
    }
	public function toAccountManage(){
    		$pageName = $_GET['pageName'];
	    	$params = array(
	    		'pageName' => $pageName
	    	);
	    	$this -> assign($params);
    		$this -> display("Admin/AccountManage/index");   	
    }

    // 账号 增、删
    public function accountAdd() {
    	header("Content-Type:text/html; charset=utf-8");
    	$User = D('Admin');
    	$result = $User -> where(array('phone' => $_POST['phone'])) -> select();
    	if (!$result) {
    		if ($User -> data($_POST) -> add()) {
    			$params = array(
    				'status' => 'success', 
    				'info' => '账号添加成功!',
    				'pageName' => 'accountManage'
    			);
	    		$this -> redirect('Admin/toAccountManage', $params);
	    	}else {
	    		$params = array(
    				'status' => 'fail',
    				'info' => '账号添加失败!',
    				'pageName' => 'accountManage'
    			);
    			$this -> redirect('Admin/toAccountManage', $params);
	    	}	
    	}else {
    		$params = array(
				'status' => 'fail',
				'info' => '账号已存在!',
				'pageName' => 'accountManage'
			);
			$this -> redirect('Admin/toAccountManage', $params);
    	}

    }

    public function accountDelete() {
        header("Content-Type:text/html; charset=utf-8");
    	$User = D('Admin');
    	$condition = array(
    		'id' => $_GET['deleteId']
    	);
    	if ($User -> where($condition) -> delete()) {
    		$params = array(
				'status' => 'success',
				'info' => '账号删除成功!',
				'pageName' => 'accountManage'
			);
    		$this -> redirect('Admin/toAccountManage', $params);
    	}else {
    		$params = array(
				'status' => 'success',
				'info' => '账号删除失败!',
				'pageName' => 'accountManage'
			);
			$this -> redirect('Admin/toAccountManage', $params);
    	}

    }
       

}