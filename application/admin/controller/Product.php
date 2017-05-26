<?php
	namespace app\admin\controller;
	use think\Db;
	use app\common\controller\AdminBase;
	use think\Session;

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
			$infocatid=$catid;
			$map['status']=1;
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
			$this->assign("infocatid",$infocatid);
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
			if($this->request->isPost()){
				$catid=$param['catid'];
				$name=$param['name'];
				$arr['catid']=$catid;
				$arr['title']=$name;
				$arr['addtime']=time();
				$uid=Session::get('uid');
				$userinfo=Db::name('member')->where('userid',$uid)->find();
				$arr['writer']=$userinfo['truename'];
				$file = request()->file('image');
				if($file){
				   // 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->validate(['size'=>15678000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
					if($info){
					    $arr['litpic']=$info->getSavename(); 
					}else{
					    echo $file->getError();
					    exit;
					}
				}
				$res=Db::name('product')->insert($arr);
				if($res){
					$itemid=Db::name('product')->getLastInsID();
					$param['id']=$itemid;
					unset($param['name']);
					$inres=Db::name('product_attr')->insert($param);
					if($inres){
						$this->success('添加产品信息成功');
					}else{
						$this->error('添加食品信息失败');
					}

				}else{
					$this->error('添加失败');
				}

			}else{
				$catid=isset($param['catid'])?$param['catid']:0;
				$name=isset($param['name'])?$param['name']:'';
				$this->assign('name',$name);
				$this->assign('catid',$catid);
				if($catid){
					//该分类的属性列表
					$sxlist=Db::name('attr_category')->alias('a')->join('attr b','a.attrid=b.id')->where('a.catid',$catid)->select();
					//该分类的具体选项
					$lists=array();
					foreach ($sxlist as $k => $v) {
						$son=array();
						$son=Db::name('attr_detail')->where('catid',$v['catid'])->where('attrid',$v['attrid'])->select();
						$v['son']=$son;
						$sxlist[$k]=$v;
					}
					$this->assign('sxlist',$sxlist);
					
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
		/**
		 * 编辑产品
		 * @Author   黄传东
		 * @DateTime 2017-05-26T15:53:25+0800
		 * @return   [type]                   [description]
		 */
		public function edit(){
			$param=$this->request->param();
			if($this->request->isPost()){
				$catid=$param['catid'];
				$name=$param['name'];
				$arr['id']=$param['id'];
				$arr['catid']=$catid;
				$arr['title']=$name;
				$arr['addtime']=time();
				$uid=Session::get('uid');
				$userinfo=Db::name('member')->where('userid',$uid)->find();
				$arr['writer']=$userinfo['truename'];
				$file = request()->file('image');
				if($file){
				   // 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->validate(['size'=>15678000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
					if($info){
					    $arr['litpic']=$info->getSavename(); 
					}else{
					    echo $file->getError();
					    exit;
					}
				}
				$res=Db::name('product')->update($arr);
				if($res){
					unset($param['name']);
					$inres=Db::name('product_attr')->update($param);
					
					$this->success('编辑产品信息成功');
					

				}else{
					$this->error('编辑产品信息失败');
				}
			}else{
				isset($param['id']) or $this->error('非法访问');

				$info=Db::name('product')->where('id',$param['id'])->find();
				$infocatid=$info['catid'];
				//该分类有的属性
				$sxlist=Db::name('attr_category')->alias('a')->join('attr b','a.attrid=b.id')->where('a.catid',$infocatid)->select();
				foreach ($sxlist as $k => $v) {
					$son=array();
					$son=Db::name('attr_detail')->where('catid',$v['catid'])->where('attrid',$v['attrid'])->select();
					$v['son']=$son;
					$sxlist[$k]=$v;
				}
				//产品分类
				$yiji=Db::name('category')->where('parentid',0)->where('status',1)->order('paixu','catid')->select();
				foreach($yiji as $k=>$v){
					$catid=$v['catid'];
					$res=Db::name('category')->where('parentid',$catid)->where('status',1)->order('paixu','catid')->select();
					$erji[$catid]=$res;
				}
				$attrinfo=Db::name('product_attr')->where('id',$param['id'])->find();
				$this->assign('id',$param['id']);
				$this->assign("yiji",$yiji);
				$this->assign("erji",$erji);
				$this->assign('infocatid',$infocatid);
				$this->assign('info',$info);
				$this->assign('sxlist',$sxlist);
				$this->assign('attrinfo',$attrinfo);
				return $this->fetch();
			}
		}
		/**
		 * 删除产品
		 * @Author   黄传东
		 * @DateTime 2017-05-26T16:46:54+0800
		 * @return   [type]                   [description]
		 */
		public function del(){
			$param=$this->param;
			isset($param['id']) or $this->error('非法访问');
			$arr['status']=0;
			$res=Db::name('product')->where("id",$param['id'])->update($arr);
			if($res){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
	}
	
?>