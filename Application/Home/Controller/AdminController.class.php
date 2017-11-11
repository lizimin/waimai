<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
	// 页面跳转
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
    

}