<?php

namespace Weixin\Model;

use Think\Exception;
use Think\Model;

class WechatModel extends Model {
	private $appid='wxbd656d12592b4e0c';
	private $mch_id='1487467822';
	private $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
	private $payKey='dfh344df98f6368sd5h8d34h8d34dr87';
	private $values=[];
	public function __construct(){
		$this->values['appid']=$this->appid;
		$this->values['mch_id']=$this->mch_id;
		$this->values['nonce_str']=$this->getNonce_str();
		$this->values['spbill_create_id']='127.0.0.1';
		$this->values['trade_type']='JSPAI';
		$this->values['notify_url']='http://'.$_SERVER['HTTP_HOST'].'/weixin.php';
	
	}

	public function request(){
		$requestPayXML=$this->ToXml();
		$rs = $this->postXmlCurl($this->ToXml(),$this->url);
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
		$this->values['total']=$value;
	}
	//生成BODY
	public function setBody($value){
		$this->values['body']=$value;
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
	
	
}