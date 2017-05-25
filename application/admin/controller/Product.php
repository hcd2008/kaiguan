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
		/**
		 * 添加产品
		 * @Author   hcd
		 * @DateTime 2017-05-25T23:19:43+0800
		 * @version  [version]
		 */
		public function add(){
			$param=$this->request->param();
			$catid=isset($param['catid'])?$param['catid']:0;
			$this->assign('catid',$catid);
			if($catid){
				//该分类的属性列表
				$sxlist=Db::name('attr_category')->alias('a')->join('attr b','a.attrid=b.id')->where('a.catid',$catid)->select();
				$this->assign('sxlist',$sxlist);
				//该分类的具体选项
				$xxlist=Db::name('attr_detail')->where('catid',$catid)->select();
				print_r($xxlist);
			}
			//产品分类
			$yiji=Db::name('category')->where('parentid',0)->where('status',1)->order('paixu','catid')->select();
			foreach($yiji as $k=>$v){
				$catid=$v['catid'];
				$res=Db::name('category')->where('parentid',$catid)->where('status',1)->order('paixu','catid')->select();

				$erji[$catid]=$res;
			}

			$this->assign("yiji",$yiji);
			$this->assign("erji",$erji);
			return $this->fetch();
		}
	}
	
?>