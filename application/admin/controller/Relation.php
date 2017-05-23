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
			$zdlist=array();
			//字段
			foreach ($res as $k => $v) {
				$catid=$v['catid'];
				$catarr=Db::name('attr_category')->alias('a')->join('hcd_category b','a.catid=b.catid')->join('hcd_attr c','a.attrid=c.id')->where('a.catid',$catid)->select();
				$zdlist[$catid]=$catarr;
			}
			$this->assign('lists',$res);
			$this->assign('zdlist',$zdlist);
			return $this->fetch();
		}
	}
?>