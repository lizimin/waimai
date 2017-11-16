<?php

namespace Weixin\Controller;

use Think\Controller;
use Think\Exception;
use Weixin\Model\WechatModel;

class WechatController extends Controller {
	private $url = '';
	private $appid = 'wx203e1116b8467c61';
	private $secret = 'c6e637dd771534cac44cce81f6c709cd';
	public function payCallback() {
		echo "ccvv1234";
	}
	public function getOpenid() {
		if (IS_POST) {
			if ($_POST ['errMsg'] == 'login:ok') {
				$this->url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->secret . '&js_code=' . $_POST ['code'] . '&grant_type=authorization_code';
				$content = http_get ( $this->url );
				$openid = json_decode ( $content, JSON_UNESCAPED_UNICODE )['openid'];
				if ($content) {
					if (! $this->checkOpenid ( $openid )) {
						$model = M ( 'weixin_user' );
						if ($last_id = $model->add ( [ 
								'openid' => $openid 
						] )) {
							return $this->ajaxReturn ( [ 
									'code' => 0,
									'info' => 'openid get successed',
									'weixin_user_id' => $last_id 
							] );
						} else {
							throw new Exception ( 'openid add to db failed' );
						}
					} else {
						return $this->ajaxReturn ( [ 
								'code' => 0,
								'info' => 'openid has arealdy',
								'weixin_user_id' => $this->checkOpenid ( $openid ) 
						] );
					}
				}
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'msg' => 'openid获取失败' 
				] );
			}
		}
	}
	public function saveUserinfo() {
		if (IS_POST) {
			if (isset ( $_POST ['rawData'] ) && isset ( $_POST ['weixin_user_id'] )) {
				$rawData = json_decode ( $_POST ['rawData'], JSON_UNESCAPED_UNICODE );
				$id = $_POST ['weixin_user_id'];
				$model = M ( 'weixin_user' );
				$data ['nickname'] = $rawData ['nickName'];
				$data ['city'] = $rawData ['city'];
				$data ['province'] = $rawData ['province'];
				$data ['img'] = $rawData ['avatarUrl'];
				if ($model->where ( 'id="' . $id . '"' )->save ( $data )) {
					$this->ajaxReturn ( [ 
							'code' => 0,
							'info' => 'success' 
					] );
				} else {
					$this->ajaxReturn ( [ 
							'code' => 0,
							'info' => 'update already ' 
					] );
				}
			} else {
				$this->ajaxReturn ( [ 
						'code' => 1,
						'info' => 'params error' 
				] );
			}
		}
	}
	private function checkOpenid($openid) {
		$model = M ( 'weixin_user' );
		$data = $model->where ( 'openid="' . $openid . '"' )->select ();
		if ($data) {
			return $data [0] ['id'];
		} else {
			return false;
		}
	}
	public function getStore() {
		$store = M ( 'store' );
		$data = $store->select ();
		$this->ajaxReturn ( $data );
	}
	public function getdish() {
		if ($_GET ['id']) {
			$Model = new \Think\Model ();
			$sql_cate = 'select *from think_category where store_id=' . $_GET ['id'];
			$sql_dish = 'select s.id as store_id,s.store_name,s.address,s.contact,c.id as category_id,c.category_name,d.id as dish_id,d.dish_name,d.price,d.img,d.discount from think_store s join think_category c on s.id=c.store_id join think_dish d on c.id=d.category_id where s.id=' . $_GET ['id'];
			$category = $Model->query ( $sql_cate );
			$dish = $Model->query ( $sql_dish );
			foreach ( $category as $k => $v ) {
				foreach ( $dish as $i => $j ) {
					if ($v ['id'] == $j ['category_id']) {
						$j ['flag'] = 0;
						$j ['total'] = 0;
						$category [$k] ['child'] [] = $j;
					}
				}
			}
			return $this->ajaxReturn ( $category );
		}
	}
	public function addressAdd() {
		if (IS_POST) {
			if (isset ( $_POST ['weixin_user_id'] ) && isset ( $_POST ['address'] ) && isset ( $_POST ['street'] ) && isset ( $_POST ['phone'] )) {
				$address = M ( 'address' );
				if ($address->add ( $address->create ( $_POST ) )) {
					return $this->ajaxReturn ( [ 
							'code' => 0,
							'info' => 'add successed',
							'data' => [ ] 
					] );
				} else {
					return $this->ajaxReturn ( [ 
							'code' => 1,
							'info' => 'add fail',
							'data' => [ ] 
					] );
				}
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'info' => 'params error ,login fail',
						'data' => [ ] 
				] );
			}
		}
	}
	public function addressDelete() {
		if (IS_GET) {
			if ($_GET ['id']) {
				$model = M ( 'address' );
				if ($model->where ( 'id=' . $_GET ['id'] )->delete ()) {
					return $this->ajaxReturn ( [ 
							'code' => 0,
							'info' => '删除成功',
							'data' => [ ] 
					] );
				} else {
					$msg = $model->getDbError ();
					return $this->ajaxReturn ( [ 
							'code' => 1,
							'info' => $msg,
							'data' => [ ] 
					] );
				}
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'info' => '缺少参数 ,请求失败',
						'data' => [ ] 
				] );
			}
		}
	}
	public function addressGet() {
		if (IS_GET) {
			if ($_GET ['weixin_user_id']) {
				$model = M ( "address" );
				$data = $model->where ( "weixin_user_id='" . $_GET ['weixin_user_id'] . "'" )->select ();
				return $this->ajaxReturn ( [ 
						'code' => 0,
						'info' => 'query success',
						'data' => $data 
				] );
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'info' => '缺少参数',
						'data' => [ ] 
				] );
			}
		}
	}
	public function addressEdit() {
		if (IS_POST) {
			if ($_POST ['id']) {
				$model = M ( 'address' );
				if ($model->where ( "id=" . $_POST ['id'] )->save ( $_POST )) {
					return $this->ajaxReturn ( [ 
							'code' => 0,
							'info' => 'update success',
							'data' => [ ] 
					] );
				} else {
					echo $model->getDbError ();
				}
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'info' => '缺少参数',
						'data' => [ ] 
				] );
			}
		}
	}
	public function getOrder() {
		if (IS_GET) {
			$weixin_user_id = $_GET ['weixin_user_id'];
			if (isset ( $_GET ['weixin_user_id'] ) && isset ( $_GET ['status'] )) {
				$model = M ( 'order' );
				$where ['weixin_user_id'] = $weixin_user_id;
				if ($_GET ['status'] == 0) {
					$where ['status'] = 0;
				}
				if ($_GET ['status'] == 1) {
					$where ['status'] = 1;
				}
				if ($_GET ['status'] == 2) {
					$where ['status'] = 2;
				}
				if ($_GET ['status'] == 3) {
					$where ['status'] = 3;
				}
				$myOrder = array ();
				$data = M ( 'order' )->where ( $where )->select ();
				foreach ( $data as $k => $v ) {
					$myOrder [$k] ['date'] = $v ['date'];
					$myOrder [$k] ['status'] = $v ['status'];
					$myOrder [$k] ['dishData'] = json_decode ( $v ['dishdata'] );
					$myOrder [$k] ['id'] = $v ['id'];
				}
				$this->ajaxReturn ( [ 
						'code' => 0,
						'info' => 'Data return success',
						'data' => $myOrder 
				] );
			} else {
				$this->ajaxReturn ( [ 
						'code' => 1,
						'info' => 'params error',
						'data' => [ ] 
				] );
			}
		}
	}
	public function createOrder() {
		header ( 'Content-Type:application/json; charset=utf-8' );
		if (IS_POST) {
			$model = new WechatModel ();
			$orderID = "B" . mt_rand ( 10000, 99999 ) . time ();
			$trade_no = date ( 'Ymdhis' ) . mt_rand ( 11, 99 ) . mt_rand ( 1, 9 );
			$order = M ( 'order' );
			if (isset ( $_POST ['address_id'] ) && isset ( $_POST ['weixin_user_id'] ) && isset ( $_POST ['total'] ) && isset ( $_POST ['dishData'] )) {
				$_POST ['trade_no'] = $trade_no;
				$_POST ['orderid'] = $orderID;
				$_POST ['date'] = date ( 'Y-m-d:h:m:s', time () );
				if ($order_id = $order->add ( $order->create ( $_POST ) )) {
					echo json_encode ( [ 
							'code' => 0,
							'info' => 'successful add',
							'data' => json_decode ( $_POST ['dishData'] ) 
					], JSON_UNESCAPED_UNICODE );
				} else {
					return $this->ajaxReturn ( [ 
							'code' => 1,
							'info' => 'params error',
							'data' => [ ] 
					] );
				}
			}
			// $model->setBody ( 'this is a black cat' );
			// $model->setTrade_no ( $trade_no );
			// $model->setTotal ( $_POST ['total'] );
		}
	}
	public function brandApply() {
		if (IS_POST) {
			$model = M ( 'brand_apply' );
			if ($model->add ( $model->create ( $_POST ) )) {
				return $this->ajaxReturn ( [ 
						'code' => 0,
						'info' => 'add suuccess',
						'data' => [ ] 
				] );
			} else {
				return $this->ajaxReturn ( [ 
						'code' => 1,
						'info' => 'params error',
						'data' => [ ] 
				] );
			}
		}
	}

}