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
			$param=$this->param;
			$catid=isset($param['catid'])?$param['catid']:0;
			$map=array();
			if($catid){
				$map['catid']=$catid;
			}
			$keyword=isset($param['keyword'])?$param['keyword']:'';
			if($keyword!=''){
				$map['title']=array('like','%'.$keyword.'%');
			}
			$res=Db::name('product')->where($map)->order('id desc')->paginate(20);
			$catarr=Db::name('category')->select();
			foreach($catarr as $k=>$v){
				$catlist[$v['catid']]=$v['catname'];
			}
			$this->assign('catlist',$catlist);
			$this->assign('lists',$res);
			//产品分类
			$yiji=Db::name('category')->where('parentid',0)->where('status',1)->order('paixu','catid')->select();
			foreach($yiji as $k=>$v){
				$catid=$v['catid'];
				$res=Db::name('category')->where('parentid',$catid)->where('status',1)->order('paixu','catid')->select();

				$erji[$catid]=$res;
			}
			$this->assign("yiji",$yiji);
			$this->assign("erji",$erji);
			$this->assign("keyword",$keyword);
			$this->assign("catid",$catid);
			return $this->fetch();
		}
	}
	
?>