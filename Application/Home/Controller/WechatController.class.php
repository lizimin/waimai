<?php
namespace Home\Controller;
use Think\Controller;
class WechatController extends Controller {
    //生成二维码
    public function createTDC() {
        header('content-type:text/html;charset=utf-8');
        //获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $data = array(
            'grant_type' => 'client_credential',
            'appid' => 'wx4cf1ee506aff078f',
            'secret' => '4cbae13f860cbaccdd4a2c6c7d7b5244'
        );
        $result = $this -> http($url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
        $token = json_decode($result);
        $token = $token->access_token;

        //获取二维码
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$token;
        $data1 = array(
            'path' => 'pages/index/index',
            'width' => '430',
            'auto_color' => false
        );
        $result = $this -> http($url, $data1, 'POST', array("Content-type: text/html; charset=utf-8"));
        $this -> ajaxReturn($result);
    }

    //用户添加(注册)
    public function userAdd(){
        header('content-type:text/html;charset=utf-8');
        $Dao = D('User');
        
        $phoneNumber = $_POST['phone'];
        $openId = $_POST['open_id'];

        $result = $Dao -> where(array('phone' => $phoneNumber)) -> select();
        $resultOpenId = $Dao -> where(array('open_id' => $openId)) -> select();
        //电话和open_id不存在 continue
        if (!$result && !$resultOpenId) {
            $condition = array(
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'open_id' => $_POST['open_id'],
                'level' => '1',
                'score' => '0'
            );
            if ($Dao -> data($condition) -> add()) {
                $param = array(
                    'code'=> '200',
                    'status'=> 'success',
                    'data' => $condition
                );
                $this -> ajaxReturn($param);
            }else {
                $param = array(
                    'code'=> '400',
                    'status'=> 'fail',
                    'info' => '注册失败'
                );
                $this -> ajaxReturn($param);
            }
        }else {
            $param = array(
                'code'=> '400',
                'status'=> 'fail',
                'info' => '该号码已存在'
            );
            $this -> ajaxReturn($param);
        }
    }

    //用户手机号更换
    public function userPhoneUpdate() {
        header("Content-Type:text/html; charset=utf-8");
        $Dao = D('User');
        $oldPhone = $_POST['old_phone'];
        $newPhone = $_POST['new_phone'];

        $result = $Dao -> where(array('phone' => $oldPhone)) -> select();
        if ($result) {
            if ($Dao -> where(array('id' => $result[0]['id'])) -> save(array('phone' => $newPhone))) {
                $param = array(
                    'code'=> '200',
                    'status'=> 'success'
                );
                $this -> ajaxReturn($param);
            }else {
                $param = array(
                    'code'=> '400',
                    'status'=> 'fail'
                );
                $this -> ajaxReturn($param);
            }
        }else {
            $param = array(
                'code'=> '400',
                'status'=> 'fail',
                'info' => '老手机号不存在'
            );
            $this -> ajaxReturn($param);
        }
    }

    //酒店查询 所有酒店查询
    public function hotelSelectAll(){
        header("Content-Type:text/html; charset=utf-8");
        $Hotel = D('Hotel');
        $HotelHomeStyle = D('Hotel_home_style');
        $HotelMember = D('Hotel_member');
        $newArr = array();

        $result = $Hotel -> order('id desc') -> select();
        foreach($result as $key => $val) {
            array_push($newArr, $val);
            $homeStyleId = explode('、', $val['home_style_id']);
            foreach($homeStyleId as $k => $v) {
                $HomeStyleArr = $HotelHomeStyle -> where(array('id' => $v)) -> order('id desc') -> select(); //homestyle array
                foreach($HomeStyleArr as $key1 => $val1) {
                    $memberIdArr = explode('、', $val1['member_id']);
                    $newArr[$key]['homeStyle'][$k]['name'] = $val1['name'];
                    $newArr[$key]['homeStyle'][$k]['price'] = $val1['price'];
                    $newArr[$key]['homeStyle'][$k]['spec'] = $val1['spec'];
                    $newArr[$key]['homeStyle'][$k]['image'] = $val1['image'];
                    foreach($memberIdArr as $key2 => $val2) {
                        $memberArr = $HotelMember -> where(array('id' => $val2)) -> select(); //hotel member
                        foreach($memberArr as $key3 => $val3){
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['name'] = $val3['name'];
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['price'] = $val3['price'];
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['spec'] = $val3['spec'];
                        }
                    }
                }
            }
        }

        $param = array(
            'code'=> '200',
            'status'=> 'success',
            'data' => $newArr
        );

        $this -> ajaxReturn($param);
    }

    //酒店查询 根据区域查询酒店
    public function hotelSelectByArea(){
        header("Content-Type:text/html; charset=utf-8");
        $area = $_POST['area'];

        $Hotel = D('Hotel');
        $HotelHomeStyle = D('Hotel_home_style');
        $HotelMember = D('Hotel_member');
        $newArr = array();

        $result = $Hotel -> where(array('area' => $area)) -> order('id desc') -> select();
        foreach($result as $key => $val) {
            array_push($newArr, $val);
            $homeStyleId = explode('、', $val['home_style_id']);
            foreach($homeStyleId as $k => $v) {
                $HomeStyleArr = $HotelHomeStyle -> where(array('id' => $v)) -> order('id desc') -> select(); //homestyle array
                foreach($HomeStyleArr as $key1 => $val1) {
                    $memberIdArr = explode('、', $val1['member_id']);
                    $newArr[$key]['homeStyle'][$k]['name'] = $val1['name'];
                    $newArr[$key]['homeStyle'][$k]['price'] = $val1['price'];
                    $newArr[$key]['homeStyle'][$k]['spec'] = $val1['spec'];
                    foreach($memberIdArr as $key2 => $val2) {
                        $memberArr = $HotelMember -> where(array('id' => $val2)) -> select(); //hotel member
                        foreach($memberArr as $key3 => $val3){
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['name'] = $val3['name'];
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['price'] = $val3['price'];
                            $newArr[$key]['homeStyle'][$k]['member'][$key2]['spec'] = $val3['spec'];
                        }
                    }
                }
            }
        }

        $param = array(
            'code'=> '200',
            'status'=> 'success',
            'data' => $newArr
        );

        $this -> ajaxReturn($param);
    }

    //全部用户查询
    public function userSelect(){
    	header('content-type:text/html;charset=utf-8');
    	$Dao = D('User');
    	if ($result = $Dao -> select()) {
    		$param = array(
	    		'code'=> '200',
	    		'status'=> 'success',
	    		'data'=> $result
	    	);
	    	$this -> ajaxReturn($param);
    	}else {
    		$param = array(
	    		'code'=> '400',
	    		'status'=> 'fail'
	    	);
	    	$this -> ajaxReturn($param);
    	}
    }

    //订单添加
    public function orderAdd() {
    	header('content-type:text/html;charset=utf-8');
        $Dao = D('Order');
        $User = D('User');
        $condition = array(
        	'order_number' => $_POST['order_number'],      //订单号
            'hotel_id' => $_POST['hotel_id'],              //酒店id
        	'hotel_name' => $_POST['hotel_name'],          //酒店名
            'hotel_phone' => $_POST['hotel_phone'],        //酒店电话
            'theme' => $_POST['theme'],                    //主题
            'address' => $_POST['address'],                //地址
        	'check_in' => $_POST['check_in'],              //入住日期
        	'check_out' => $_POST['check_out'],            //退房日期
            'used_score' => $_POST['used_score'],          //抵扣积分
            'now_score' => $_POST['now_score'],            //现有积分
            'cost_price' => $_POST['cost_price'],          //原价
        	'price' => $_POST['price'],                    //总价
        	'status' => $_POST['status'],                  //状态
            'detail' => $_POST['detail'],                  //详情
            'user_name' => $_POST['user_name'],            //用户名
            'user_phone' => $_POST['user_phone'],          //用户电话
            'view' => 0                                    //是否浏览
        );
        if ($Dao -> data($condition) -> add()) {
            //积分扣除
            $User -> where(array('phone' => $_POST['user_phone'])) -> save(array('score' => $_POST['now_score']));
        	$param = array(
        		'code'=> '200',
        		'status'=> 'success'
        	);
        	$this -> ajaxReturn($param);
        }else {
        	$param = array(
        		'code'=> '400',
        		'status'=> 'fail'
        	);
        	$this -> ajaxReturn($param);
        }
    }

    //全部订单查询
    public function orderSelect() {
    	header('content-type:text/html;charset=utf-8');
        $Dao = D('Order');
        $userPhone = $_POST['phone'];
        $result = $Dao -> where(array('user_phone' => $userPhone)) -> select();
        if ($result) {
    		$param = array(
	    		'code'=> '200',
	    		'status'=> 'success',
	    		'data'=> $result
	    	);
	    	$this -> ajaxReturn($param);
    	}else {
    		$param = array(
	    		'code'=> '400',
	    		'status'=> 'fail'
	    	);
	    	$this -> ajaxReturn($param);
    	}
    }

    //订单状态更改
    public function orderStatusUpdate() {
        header("Content-Type:text/html; charset=utf-8");
        $Dao = D('Order');

        $refundReason = '';
        $id = $_POST['id'];
        $status = $_POST['status'];
        if ($_POST['refund_reason']) {
            $refundReason = $_POST['refund_reason'];   
        }
        $condition = array(
            'status' => $status,
            'refund_reason' => $refundReason
        );

        if ($Dao -> where(array('id' => $id)) -> save($condition)) {
            $params = array(
                'code' => '200',
                'status' => 'success'
            );
            $this -> ajaxReturn($params);
        }else {
            $params = array(
                'code' => '400',
                'status' => 'fail'
            );
            $this -> ajaxReturn($params);
        }
    }

    //根据状态查询订单
    public function orderSelectByStatus() {
        header('content-type:text/html;charset=utf-8');
        $Dao = D('Order');
        $userPhone = $_POST['phone'];
        $condition = array();

        if ($_POST['status'] == '-1') {
            
        }else {
            $condition = array(
                'status' => $_POST['status']
            );
        }

        $condition['user_phone'] = $userPhone;

        $result = $Dao -> where($condition) -> order('id desc') -> select();
        if ($result) {
            $param = array(
                'code'=> '200',
                'status'=> 'success',
                'data'=> $result
            );
            $this -> ajaxReturn($param);
        }else {
            $param = array(
                'code'=> '400',
                'status'=> 'fail'
            );
            $this -> ajaxReturn($param);
        }   
    }

    //单一订单查询(id)
    public function orderSelectById() {
        header('content-type:text/html;charset=utf-8');
        $Dao = D('Order');

        $condition = array(
            'id' => $_POST['id']
        );

        $result = $Dao -> where($condition) -> order('id desc') -> select();
        if ($result) {
            $param = array(
                'code'=> '200',
                'status'=> 'success',
                'data'=> $result
            );
            $this -> ajaxReturn($param);
        }else {
            $param = array(
                'code'=> '400',
                'status'=> 'fail'
            );
            $this -> ajaxReturn($param);
        }      
    }

    //腾讯地图 地址转经纬度
    public function mapAddressTranslation() {
        header('content-type:text/html;charset=utf-8');
        $address = $_GET['address'];
        $url = 'http://api.map.baidu.com/geocoder/v2/';
        $data = array(
            'address' => $address,
            'ak' => '9GdMiuswChEEoGiPFCsi68QGZXQiH1aH',
            'output' => 'json'
        );
        $result = $this -> http($url, $data, 'POST', array("Content-type: text/html; charset=utf-8"));
        $this -> ajaxReturn($result);
    }

    //小程序用户登录
    public function userLogin() {
        header('content-type:text/html;charset=utf-8');
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $appId = 'wx4cf1ee506aff078f';
        $secret = '4cbae13f860cbaccdd4a2c6c7d7b5244';
        $jsCode = $_GET['code'];
        $grantType = 'authorization_code';

        $data = array(
            'appid' => $appId,
            'secret' => $secret,
            'js_code' => $jsCode,
            'grant_type' => $grantType
        );

        $result = $this -> http($url, $data, 'GET', array("Content-type: text/html; charset=utf-8"));
        $this -> ajaxReturn($result);
    }

    //小程序登录状态验证
    public function userLoginStatusConfirm() {
        header('content-type:text/html;charset=utf-8');
        $Dao = D('User');
        $openId = $_POST['open_id'];

        $result = $Dao -> where(array('open_id' => $openId)) -> select();
        if ($result) {
            $param = array(
                'code'=> '200',
                'status'=> 'success',
                'data'=> $result[0]
            );
            $this -> ajaxReturn($param);
        }else {
            $param = array(
                'code'=> '400',
                'status'=> 'fail'
            );
            $this -> ajaxReturn($param);
        }  
    }

    //http请求
    function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }
}