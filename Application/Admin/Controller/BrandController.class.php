<?php

namespace Admin\Controller;

use Think\Exception;

class BrandController extends AdminController {
	public function toBrand() {
		$model = M ( 'brand' );
		$data = $model->query ( "select u.*,b.brand_name,b.address,b.img brand_img,b.contact,b.describe from think_user u join think_brand b on u.brand_id=b.id where u.type=0" );
		$this->assign('pageName','brand');
		$this->assign ( 'data', $data );
		$this->display ( 'Admin/Brand/index' );
	}
	public function brandAdd() {
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
			
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功
				$user = M ( 'user' );
				$brand = M ( 'brand' );
				$userData ['username'] = $_POST ['username'];
				$userData ['password'] = $_POST ['password'];
				$userData ['nickname'] = $_POST ['nickname'];
				$userData ['phone'] = $_POST ['phone'];
				if (isset ( $info ['userimg'] )) {
					$userData ['img'] = '/Upload/' . $info ['userimg'] ['savepath'] . $info ['userimg'] ['savename'];
				}
				$userData ['type'] = 0;
				$brandData ['brand_name'] = $_POST ['brand_name'];
				$brandData ['address'] = $_POST ['address'];
				if (isset ( $info ['img'] )) {
					$brandData ['img'] = '/Upload/' . $info ['img'] ['savepath'] . $info ['img'] ['savename'];
				}
				$brandData ['contact'] = $_POST ['contact'];
				$brandData ['describe'] = $_POST ['describe'];
				
				if ($brand_id = $brand->add ( $brand->create ( $brandData ) )) {
					$userData ['brand_id'] = $brand_id;
					if ($user->add ( $user->create ( $userData ) )) {
						$params = array(
								'status' => 'success',
								'info' => '商户添加成功!',
								'pageName' => 'brand'
						);
						$this->redirect ( 'Brand/tobrand',$params);
					} else {
						die ( $user->getDbError () );
					}
				} else {
					die ( $brand->getDbError () );
				}
			}
		}
	}
	public function brandDelete() {
		if (IS_GET) {
			$user_id = $_GET ['id'];
			$user = M ( 'user' );
			$brand = M ( 'brand' );
			$brand_id = $user->field ( 'brand_id' )->where ( 'id=' . $user_id )->select ()[0]['brand_id'];
			try {
				if (isset ( $brand_id )) {
					if ($brand->where ( 'id=' . $brand_id )->delete ()) {
						if ($user->where ( 'id=' . $user_id )->delete ()) {
							$params = array(
									'status' => 'success',
									'info' => '商户删除成功!',
									'pageName' => 'brand'
							);
							$this->redirect ( 'Brand/tobrand',$params );
						} else {
							throw new Exception ( '删除错误', 1 );
						}
					} else {
						throw new Exception ( '删除错误', 1 );
					}
				} else {
					throw new Exception ( 'brand_id不存在', 1 );
				}
			} catch ( Exception $e ) {
				echo $e->getMessage ();
			}
		}
	}
	
	public function brandApply(){
		$model=M('brand_apply');
		$data=$model->select();
		$this->assign('data',$data);
		$this->display('Admin/Brand/brandApply');
	}
	
	public function brandConfirm() {
		if (IS_GET) {
			if (isset ( $_GET ['id'] )) {
				$data['status']=1;
				$model = M ( 'brand_apply' );
				if($model->where('id='.$_GET['id'])->save($data)){
					$params = array(
							'status' => 'success',
							'info' => '确认成功!',
							'pageName' => 'brand'
					);
					$this->redirect ( 'Brand/brandApply',$params );
				}else{
					$params = array(
							'status' => 'success',
							'info' => '确认失败!',
							'pageName' => 'brand'
					);
					$this->redirect ( 'Brand/brandApply',$params );
				}
			}
		}
	}
}