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
			session_start();
			$param=$this->request->param();
			$catid=isset($param['catid'])?$param['catid']:0;
			$map['catid']=$catid;
			unset($param['catid']);
			$catinfo=Db::name('category')->where('catid',$catid)->find();
			if($catid==0){
				$this->error('非法访问');
			}
			
			//产品信息表
			$map['catid']=$catid;
			unset($param['catid']);
			//用来存储已选择的选项
			$tjidarr=array();
			if(count($param)){
				//获取已存储的查询条件
				if(isset($_SESSION['tiaojian'])){
					// $cond=Session::get('tiaojian');
					$cond=$_SESSION['tiaojian'];
					foreach ($param as $aa => $bb) {
						$cond[$aa]=$bb;
					}
					$_SESSION['tiaojian']=$cond;
				}else{
					$_SESSION['tiaojian']=$param;
					$cond=$param;
				}
				
				foreach ($cond as $kk => $vv) {
					$map[$kk]=$vv;
					$tjidarr[]=$vv;
				}
			}else{
				//重置搜索条件
				unset($_SESSION['tiaojian']);
			}
			$this->assign('sxid',$tjidarr);
			//该分类可显示的字段
			$res=Db::name('attr_category')->alias('a')->join('hcd_attr b','a.attrid=b.id')->where('a.catid',$catid)->order('b.paixu')->select();
			//具体选项
			$lists=array();
			foreach ($res as $k => $v) {
				$jg=Db::name('attr_detail')->where('attrid',$v['attrid'])->where('catid',$catid)->select();
				$jieguo=array();
				//判断该字段的数量
				foreach($jg as $aa=>$bb){
					$zdsum=Db::name('product_attr')->where($map)->where($bb['attr'],$bb['id'])->count();
					$bb['sum']=$zdsum;
					$jieguo[$aa]=$bb;
				}
				$v['son']=$jieguo;
				$lists[$k]=$v;
			}
			// print_r($lists);
			//所有选项
			$alloption=Db::name('attr_detail')->where('catid',$catid)->select();
			
			foreach ($alloption as $k1 => $v1) {
				$alloptions[$v1['id']]=$v1;
			}
			//存储查询条件
			// Session::set('tiaojian',$param);
			// $product=Db::name('product_attr')->alias('a')->field('a.*,b.title,b.litpic')->join('hcd_product b','a.id=b.id')->where('a.catid',$catid)->paginate();
			$product=Db::name('product_attr')->where($map)->paginate();
			//结果数量
			$resshu=Db::name('product_attr')->where($map)->count();
			$products=array();
			foreach ($product as $k => $v) {
				$pinfo=Db::name('product')->where('id',$v['id'])->find();
				$v['title']=$pinfo['title'];
				$v['litpic']=$pinfo['litpic'];
				$products[$k]=$v;
			}
			// print_r($products);exit;
			$this->assign('catinfo',$catinfo);
			$this->assign('lists',$lists);
			$this->assign('plists',$products);
			$this->assign('chanpin',$product);
			$this->assign('alloptions',$alloptions);
			$this->assign('catid',$catid);
			$this->assign('resshu',$resshu);
			return $this->fetch();
		}
	}
?>