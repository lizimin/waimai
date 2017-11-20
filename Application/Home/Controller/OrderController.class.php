<?php

namespace Home\Controller;

class OrderController extends AdminController {
	public function orderList() {
		$model = new \Think\Model ();
		$brand_id = session ( 'brand_id' );
		if (session ( 'user_id' ) == null || session ( 'brand_id' ) == null) {
			$this->error ( 'login failed', '/Home/Login/pageLogin' );
		}
		$store_id = M ( 'store' )->field ( 'id' )->where ( 'brand_id=' . $brand_id )->select ();
		$str_storeID = '';
		foreach ( $store_id as $k => $v ) {
			$str_storeID .= ',' . $v ['id'];
		}
		$str_storeID = ltrim ( $str_storeID, ',' );
		$sql = 'select o.*,s.store_name,a.name,a.phone from think_order o join think_store s on o.store_id=s.id join think_address a on o.address_id=a.id where store_id in (' . $str_storeID . ')';
		$data = $model->query ( $sql );
		$this->assign ( 'data', $data );
		$this->display ( 'Admin/Order/orderList' );
	}
	public function orderSelectByStatusAjaxReturn() {
		if (IS_AJAX) {
			$model = new \Think\Model ();
			$brand_id = session ( 'brand_id' );
			if (session ( 'user_id' ) == null || session ( 'brand_id' ) == null) {
				$this->ajaxReturn ( [ 
						'code' => 1,
						'info' => 'fail please login again',
						'data' => [ ],
						'success'=>'fail', 
				] );
				exit ();
			}
			$store_id = M ( 'store' )->field ( 'id' )->where ( 'brand_id=' . $brand_id )->select ();
			$str_storeID = '';
			foreach ( $store_id as $k => $v ) {
				$str_storeID .= ',' . $v ['id'];
			}
			$str_storeID = ltrim ( $str_storeID, ',' );
			if (isset ( $_POST ['status'] ) && $_POST ['status'] != - 1) {
				$sql = 'select o.*,s.store_name,a.name,a.phone from think_order o join think_store s on o.store_id=s.id join think_address a on o.address_id=a.id where o.status=' . $_POST ['status'] . ' and store_id in (' . $str_storeID . ')';
			} else {
				$sql = 'select o.*,s.store_name,a.name,a.phone from think_order o join think_store s on o.store_id=s.id join think_address a on o.address_id=a.id where store_id in (' . $str_storeID . ')';
			}
			$data = $model->query ( $sql );
			$this->ajaxReturn ( [ 
					'code' => 0,
					'info' => 'success',
					'data' => $data,
					'status'=>'success'
			] );
		}

	}
	
	public function orderConfirm(){
		if(IS_GET){
			$id=$_GET['id'];
			$model=M('order');
			if($model->where("id=".$id)->save(['status'=>2])){
				$params = array (
						'status' => 'success',
						'info' => '确认成功!',
						'pageName' => 'order',
					
				);
				$this->redirect ( 'Order/orderList', $params );
			}else{
				echo $model->getError();
			}
		}
		
	}
	
	public function refoundConfirm(){
		if(IS_GET){
			$id=$_GET['id'];
			$model=M('order');
			if($model->where("id=".$id)->save(['status'=>2])){
				$params = array (
						'status' => 'success',
						'info' => '确认退款成功',
						'pageName' => 'order',
							
				);
				$this->redirect ( 'Order/orderList', $params );
			}else{
				echo $model->getError();
			}
		}
	}
}