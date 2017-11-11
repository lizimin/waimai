<?php
// Common function ------------------------------------------------
function selectAll($tableName) {
	header("Content-Type:text/html; charset=utf-8");
	$Dao = D($tableName);
	$result = $Dao -> order('id DESC') -> select();
	return $result;
}

function selectAllByAttr($tableName,$arr) {
	header("Content-Type:text/html; charset=utf-8");
	$Dao = D($tableName);
	$result = $Dao -> where($arr) -> order('id DESC') -> select();
	return $result;
}

function selectCount($tableName) {
	header("Content-Type:text/html; charset=utf-8");
	$Dao = D($tableName);
	$result = $Dao -> count();
	return $result;
}

// 从第 $index 开始，拿 $count 个数据
 function selectGetCount($tableName,$index,$count) {
	header("Content-Type:text/html; charset=utf-8");
	$count = $count+$index;
	$Dao = D($tableName);
	$result = $Dao -> order('id DESC') -> select();
	for($i=$index; $i<$count; $i++) {
		if (!empty($result[$i])) {
			$newResult[$i] = $result[$i];
		}
	}
	return $newResult;
}

 function selectGetCountAjaxReturn() {
	header("Content-Type:text/html; charset=utf-8");
	$tableName = $_POST['tableName'];
	$index = $_POST['index'];
	$count = $_POST['count'];

	$count = $count+$index;
	$Dao = D($tableName);
	$result = $Dao -> order('id DESC') -> select();
	$newResult = array();
	for($i=$index; $i<$count; $i++) {
		if (!empty($result[$i])) {
			$newResult[$i] = $result[$i];
		}
	}
	$this -> ajaxReturn($newResult);
}

function selectAllAjaxReturn() {
	header("Content-Type:text/html; charset=utf-8");
	$tableName = $_POST['tableName'];
	$Dao = D($tableName);
	$result = $Dao -> order('id DESC') -> select();
	$this -> ajaxReturn($result);
}

function deleteOne($tableName, $id){
	header("Content-Type:text/html; charset=utf-8");
	$Dao = D($tableName);
	if ($Dao -> where(array('id' => $id)) -> delete()) {
		return 'success';
	}else {
		return 'fail';
	}
}

function selectOne($tableName, $id) {
	header("Content-Type:text/html; charset=utf-8");
	$Dao = D($tableName);
	$result = $Dao -> where(array('id' => $id)) -> order('id DESC') -> select();
	return $result;
}



function sendPhoneMessage() {
	$sendModel = $_POST['send_model'];      //发送模板
	$randomNum = '';

	if ($sendModel == 'sign_up') {
		//验证码
		$randomNum = rand(100000,999999);

		$userPhone = $_POST['phone'];           //发送给某个手机号
		$content = urlencode(iconv("UTF-8","GB2312","【嘉优隆精品酒店】您好，本次操作的验证码为".$randomNum."。"));
	}else if($sendModel == 'order_remind') {
		//订单提醒
		$userPhone = '18788156679';
		$content = urlencode(iconv("UTF-8","GB2312","【嘉优隆精品酒店】您好，有一条新订单，请上后台操作。"));
	}

	$url="http://service.winic.org/sys_port/gateway/?id=%s&pwd=%s&to=%s&content=%s&time=";
	$id = urlencode("jiayoulong");
	$pwd = urlencode("jiayoulong123");
	$to = urlencode($userPhone);
	$rurl = sprintf($url, $id, $pwd, $to, $content);
	// printf("url=%s\n", $rurl);
	$ret = file($rurl);
	// print_r($ret);

	$param = array(
			'randomNum' => $randomNum
	);
	$this -> ajaxReturn($param);
}

function keyMD5() {
	$sign = $_GET['sign'];
	$this -> ajaxReturn(md5($sign));
}

function http_post($url, $data)
{
	$curl = curl_init();
	if (stripos($url, "https://") !== false) {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	}
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$content = curl_exec($curl);
	$status  = curl_getinfo($curl);
	curl_close($curl);
	if (intval($status["http_code"]) == 200) {
			//写入日志
		return $content;
	} else {
		 //写入日志
		return false;
	}
}

 function postXmlCurl($xml,$url)
{
	$url = $url;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl,CURLOPT_URL,$url);
	//curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_POST,true);
	curl_setopt($curl,CURLOPT_POSTFIELDS,$xml);
	$result=curl_exec($curl);

	if($result){
		curl_close($curl);
		return $result;
	}else{
	
		curl_close($curl);
		return false;
	}
}

function http_get($url)
{
	$curl = curl_init();
	if (stripos($url, "https://") !== false) {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	}
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($curl);
	$status  = curl_getinfo($curl);
	curl_close($curl);
	if (intval($status["http_code"]) == 200) {
		//写入日志
		return $content;
	} else {
		return false;
	}
}
