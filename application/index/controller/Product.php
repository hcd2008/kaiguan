<?php
	namespace app\index\controller;
	use think\Db;
	use think\Controller;

	class Product extends Controller{
		/**
		 * 产品分类信息筛选
		 * @Author   黄传东
		 * @DateTime 2017-05-31T15:34:36+0800
		 * @return   [type]                   [description]
		 */
		public function index(){
			$param=$this->request->param();
			$catid=isset($param['catid'])?$param['catid']:0;
			$catinfo=Db::name('category')->where('catid',$catid)->find();
			if($catid==0){
				$this->error('非法访问');
			}
			//该分类可显示的字段
			$res=Db::name('attr_category')->alias('a')->join('hcd_attr b','a.attrid=b.id')->where('a.catid',$catid)->order('b.paixu')->select();
			//具体选项
			$lists=array();
			foreach ($res as $k => $v) {
				$jg=Db::name('attr_detail')->where('attrid',$v['attrid'])->where('catid',$catid)->select();
				$v['son']=$jg;
				$lists[$k]=$v;
			}
			//所有选项
			$alloption=Db::name('attr_detail')->where('catid',$catid)->select();
			
			foreach ($alloption as $k1 => $v1) {
				$alloptions[$v1['id']]=$v1;
			}
			
			//产品信息表
			$product=Db::name('product_attr')->alias('a')->field('a.*,b.title')->join('hcd_product b','a.id=b.id')->where('a.catid',$catid)->paginate();
			// print_r($product);exit;
			$this->assign('catinfo',$catinfo);
			$this->assign('lists',$lists);
			$this->assign('plists',$product);
			$this->assign('alloptions',$alloptions);
			return $this->fetch();
		}
	}
?>