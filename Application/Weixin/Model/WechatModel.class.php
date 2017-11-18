<?php

namespace Weixin\Model;

use Think\Exception;
use Think\Model;

class WechatModel extends Model {
	const  URL='https://api.mch.weixin.qq.com/pay/unifiedorder';
	private $appid='wx203e1116b8467c61';
	private $mch_id='1492207522';
	private $payKey='f48hf5nd54h5r8ty44590fnyt823g8fg';
	private $values=[];
	public function __construct(){
		$this->values['appid']=$this->appid;
		$this->values['mch_id']=$this->mch_id;
		$this->values['nonce_str']=$this->getNonce_str();
		$this->values['spbill_create_ip']='127.0.0.1';
		$this->values['trade_type']='JSAPI';
		$this->values['notify_url']='http://'.$_SERVER['HTTP_HOST'].'/weixin.php';
	
	}

	public function request(){
		$requestPayXML=$this->ToXml();
		$rs = $this->postXmlCurl($this->ToXml());
		$result = $this->FromXml($rs);
		if (isset($result['return_code']) && $result['return_code'] == 'SUCCESS') {
			return $result;
		}else{
			return false;
		}
	}
	//把请求返回的XML转化为数组
	public function FromXml($xml)
	{
		//安全防护
		libxml_disable_entity_loader(true);
		return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	}
	//转化成XML文件；
	public function ToXml()
	{
		if(!is_array($this->values) || count($this->values) <= 0)
		{
			return false;
		}
		$xml = "<xml>";
		foreach ($this->values as $key=>$val)
		{
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}
	//生成商户号
	public function setTrade_no($value){
		$this->values['out_trade_no']=$value;
	}
	//生成价格
	public function setTotal($value){
		$this->values['total_fee']=$value;
	}
	//生成BODY
	public function setBody($value){
		$this->values['body']=$value;
	}
	//生成Openid
	public function setOpenid($value){
		$this->values['openid']=$value;
	}
	//生成随机字符串;
	public  function getNonce_str($length = 32){
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
	public function setSign(){
		$sign=$this->makeSign();
		$this->values['sign']=$sign;
		return $sign;
	}
	
	//签名算法
	private function makeSign(){
		ksort($this->values);
		$string=$this->ToUrlParams();
		$key=$this->payKey;
		$string=$string.'&key='.$key;
		$string=md5($string);
		$result=strtoupper($string);
		return $result;
		
	}
	//拼接字符串;
	public function ToUrlParams(){
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	//返回SDK签名;
	public function JSapi($prepay_id, $nonceStr){
		$appid=$this->appid;
		$timeStamp=time();
		$key=$this->payKey;
		$str="appId={$appid}&nonceStr={$nonceStr}&package=prepay_id={$prepay_id}&signType=MD5&timeStamp={$timeStamp}&key={$key}";
		$paySign = strtoupper(md5($str));
	    return ['nonceStr'=>$nonceStr,'timeStamp'=>$timeStamp,'paySign'=>$paySign,'package'=>'prepay_id='.$prepay_id];
	    
	}
	
	//发送数据
	private static function postXmlCurl($xml)
	{
		$url = self::URL;
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
	
	
}