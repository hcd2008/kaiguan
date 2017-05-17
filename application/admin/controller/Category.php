<?php
	namespace app\admin\controller;
	use think\Db;
	use app\common\controller\AdminBase;

	class Category extends AdminBase{
		/**
		 * 商品分类
		 * @Author   黄传东
		 * @DateTime 2017-05-17T15:52:18+0800
		 * @return   [type]                   [description]
		 */
		public function index(){
			$yiji=Db::name('category')->where('parentid',0)->where('status',1)->select();
			foreach($yiji as $k=>$v){
				$catid=$v['catid'];
				$res=Db::name('category')->where('parentid',$catid)->select();
				$erji[$catid]=$res;
			}
			$this->assign("yiji",$yiji);
			$this->assign("erji",$erji);
			return $this->fetch();
		}
		/**
		 * 编辑分类
		 * @Author   黄传东
		 * @DateTime 2017-05-17T17:09:01+0800
		 * @return   [type]                   [description]
		 */
		public function editCategory(){
			isset($this->param['catid']) or $this->error('非法访问');
			$info=Db::name('category')->where('catid',$this->param['catid'])->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
	}
	
?>