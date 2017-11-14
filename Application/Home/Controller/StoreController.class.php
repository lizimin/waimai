<?php

namespace Home\Controller;

use Home\Controller\AdminController;

class StoreController extends AdminController {
	public function toStore() {
		$model = M ( 'store' );
		if (session ( 'user_id' ) == null || session ( 'brand_id' ) == null) {
			$this->error ( 'login failed', '/Home/Login/pageLogin' );
		}
		$data = $model->where ( 'brand_id=' . session ( 'brand_id' ) )->select ();
		$this->assign ( 'data', $data );
		$this->assign ( 'pageName', 'store' );
		$this->display ( 'Admin/Store/index' );
	}
	public function storeAdd() {
		if (IS_POST) {
			$storeData ['store_name'] = $_POST ['store_name'];
			$storeData ['address'] = $_POST ['address'];
			$storeData ['contact'] = $_POST ['address'];
			$storeData ['describe'] = $_POST ['describe'];
			if (session ( 'user_id' ) == null || session ( 'brand_id' ) == null) {
				$this->error ( 'login failed', '/Home/Login/pageLogin' );
			}
			$storeData ['brand_id'] = session ( 'brand_id' );
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = './Upload/'; // 设置附件上传根目录
			$upload->savePath = 'img/'; // 设置附件上传（子）目录
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功
				if (isset ( $info ['img'] )) {
					$storeData ['img'] = '/Upload/' . $info ['img'] ['savepath'] . $info ['img'] ['savename'];
					$model = M ( 'store' );
					if ($model->add ( $model->create ( $storeData ) )) {
						$params = array (
								'status' => 'success',
								'info' => '店铺添加成功!',
								'pageName' => 'store' 
						);
						$this->redirect ( 'Store/toStore', $params );
					} else {
						die ( $model->getDbError () );
					}
				}
			}
		}
	}
	public function storeEdit() {
		if (IS_POST) {
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = './Upload/'; // 设置附件上传根目录
			$upload->savePath = 'img/'; // 设置附件上传（子）目录
			                            // 上传文件
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功
				if (isset ( $info ['img'] )) {
					$_POST ['img'] = '/Upload/' . $info ['img'] ['savepath'] . $info ['img'] ['savename'];
					$model = M ( 'store' );
					if ($model->where('id='.$_POST['store_id'])->save(( $model->create ( $_POST ) ))) {
						$params = array (
								'status' => 'success',
								'info' => '店铺修改成功!',
								'pageName' => 'store' 
						);
						$this->redirect ( 'Store/toStore', $params );
					} else {
						die ( $model->getDbError () );
					}
				}
			}
		}
		if (IS_AJAX) {
			$id = $_GET ['id'];
			$model = M ( 'store' );
			$data = $model->where ( 'id=' . $id )->select ()[0];
			if ($data) {
				$this->ajaxReturn ( [ 
						'code' => 0,
						'info' => 'get data success',
						'data' => $data 
				] );
			}
		}
	}
	public function storeDelete() {
		if (IS_GET) {
			$model = M ( 'store' );
			if ($model->where ( "id=" . $_GET ['id'] )->delete ()) {
				$params = array (
						'status' => 'success',
						'info' => '店铺删除成功!',
						'pageName' => 'store' 
				);
				$this->redirect ( 'Store/toStore', $params );
			} else {
				die ( $model->getDbError () );
			}
		}
	}
	public function storeManager() {
		if (IS_GET) {
			if (isset ( $_GET ['id'] )) {
				$model = M ( 'store' );
				$category = M ( 'category' );
				$category = $category->where ( 'store_id=' . $_GET ['id'] )->select ();
				$data = $model->where ( 'id=' . $_GET ['id'] )->select ()[0];
				$this->assign ( 'data', $data );
				$this->assign ( 'pageName', 'category' );
				$this->assign ( 'category', $category );
				$this->assign ( 'store_id', $data ['id'] );
				$this->display ( 'Admin/Store/storeManager' );
			}
		}
	}
	public function categoryAdd() {
		if (IS_POST) {
			$storeData ['brand_id'] = session ( 'brand_id' );
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = './Upload/'; // 设置附件上传根目录
			$upload->savePath = 'category/'; // 设置附件上传（子）目录
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功
				if (isset ( $info ['img'] )) {
					$_POST ['img'] = '/Upload/' . $info ['img'] ['savepath'] . $info ['img'] ['savename'];
					$model = M ( 'category' );
					if ($model->add ( $model->create ( $_POST ) )) {
						$params = array (
								'status' => 'success',
								'info' => '分类添加添加成功!',
								'pageName' => 'category',
								'id' => $_POST ['store_id'] 
						);
						$this->redirect ( 'Store/storeManager', $params );
					} else {
						die ( $model->getDbError () );
					}
				}
			}
		}
	}
	public function dishManager() {
		if (IS_GET) {
			$category_id = $_GET ['category_id'];
			$category = M ( 'category' )->where ( 'id=' . $category_id )->select ()[0];
			$dish = M ( 'dish' )->where ( 'category_id=' . $category_id )->select ();
			$this->assign ( 'store_id', $category ['store_id'] );
			$this->assign ( 'dish', $dish );
			$this->assign ( 'category', $category );
			$this->assign ( 'pageName', 'category' );
			$this->display ( 'Admin/Store/dishManager' );
		}
	}
	public function dishAdd() {
		if (IS_POST) {
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = './Upload/'; // 设置附件上传根目录
			$upload->savePath = 'dish/'; // 设置附件上传（子）目录
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功
				if (isset ( $info ['img'] )) {
					$_POST ['img'] = '/Upload/' . $info ['img'] ['savepath'] . $info ['img'] ['savename'];
					$model = M ( 'dish' );
					if ($model->add ( $model->create ( $_POST ) )) {
						$params = array (
								'status' => 'success',
								'info' => '分类添加添加成功!',
								'pageName' => 'category',
								'category_id' => $_POST ['category_id'] 
						);
						$this->redirect ( 'Store/dishManager', $params );
					} else {
						die ( $model->getDbError () );
					}
				}
			}
		}
	}
}