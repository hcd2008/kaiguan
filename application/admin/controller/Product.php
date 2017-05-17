<?php
	namespace app\admin\controller;
	use think\Db;
	use app\common\controller\AdminBase;

	class Product extends AdminBase{
		/**
		 * 商品分类
		 * @Author   黄传东
		 * @DateTime 2017-05-17T15:52:18+0800
		 * @return   [type]                   [description]
		 */
		public function category(){
			return $this->fetch();
		}
	}
	
?>