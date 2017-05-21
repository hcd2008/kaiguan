<?php
	namespace app\admin\controller;
	use app\common\controller\AdminBase;
	use think\Db;
	use think\Cache;

	class Relation extends AdminBase{
		/**
		 * 属性列表
		 * @Author   黄传东
		 * @DateTime 2017-05-18T11:37:54+0800
		 * @return   [type]                   [description]
		 */
		public function index(){
			$res=Db::name('category')->where('status',1)->where('parentid','<>',0)->order('paixu,catid')->select();
			$this->assign('lists',$res);
			return $this->fetch();
		}
	}
?>