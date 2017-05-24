<?php
	namespace app\admin\controller;
	use think\Db;
	use app\common\controller\AdminBase;

	class Product extends AdminBase{
		/**
		 * 产品列表
		 * @Author   黄传东
		 * @DateTime 2017-05-24T18:21:08+0800
		 * @return   [type]                   [description]
		 */
		public function index(){
			$res=Db::name('product')->order('id desc')->paginate(20);
			$catarr=Db::name('category')->select();
			foreach($catarr as $k=>$v){
				$catlist[$v['catid']]=$v['catname'];
			}
			$this->assign('catlist',$catlist);
			$this->assign('lists',$res);
			return $this->fetch();
		}
	}
	
?>