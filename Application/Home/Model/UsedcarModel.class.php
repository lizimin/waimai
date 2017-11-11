<?php
namespace Home\Model;
use Think\Model;
class UsedcarModel extends Model{
	protected $_validate=array(
		array('title','require','标题必须输入'),
		array('brand_id','require','品牌ID必须输入'),
		array('brand_id','number','品牌ID为整型'),
		array('price','require','价格必须输入'),
		array('price','number','price为整型'),
		array('mileage','number','里程数为整型'),
		array('view','number','view为整型'),
	);

}