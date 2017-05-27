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
		/**
		 * 分类属性详情
		 * @Author   黄传东
		 * @DateTime 2017-05-24T17:18:49+0800
		 * @return   [type]                   [description]
		 */
		public function detail(){
			$param=$this->param;
			$catid=$param['catid'];
			$attrid=$param['attrid'];
			$res=Db::name('attr_detail')->where('attrid',$attrid)->where('catid',$catid)->select();
			$str='';
			foreach ($res as $k => $v) {
				$str.="<option value='".$v['id']."'>".$v['title']."</option>";
			}
			return $str;
		}
		/**
		 * 为分类添加属性
		 * @Author   黄传东
		 * @DateTime 2017-05-26T17:07:22+0800
		 */
		public function addAttr(){
			$param=$this->param;
			if($this->request->isPost()){
				$catid=$param['catid'];
				$attr['catid']=$catid;
				foreach ($param['attr'] as $k => $v) {
					$attr['attrid']=$v;
					Db::name('attr_category')->insert($attr);
				}
				$this->success('添加属性成功');
			}else{
				isset($param['catid']) or $this->error('请选择分类');
				$catid=$param['catid'];
				$catinfo=Db::name('category')->where('catid',$catid)->find();
				//分类拥有的属性
				$res=Db::name('attr_category')->alias('a')->join('hcd_attr b','a.attrid=b.id')->where('a.catid',$catid)->select();
				$lists=array();
				foreach ($res as $k => $v) {
					$lists[]=$v['attrid'];
				}
				//所有属性
				$allattr=Db::name('attr')->order('paixu')->select();
				$this->assign('catinfo',$catinfo);
				$this->assign('allattr',$allattr);
				$this->assign('lists',$lists);
				return $this->fetch();
			}
		}
		/**
		 * 为分类属性添加选项
		 * @Author   黄传东
		 * @DateTime 2017-05-27T16:35:31+0800
		 */
		public function addOption(){
			$param=$this->param;
			isset($param['catid']) or $this->error('请选择分类');
			isset($param['attrid']) or $this->error('请选择属性');
			$catid=$param['catid'];
			$attrid=$param['attrid'];

			if($this->request->isPost()){
				$title=isset($param['title'])?trim($param['title']):'';
				if($title==''){
					$this->error('请填写选项名称');
				}
				//验证是否重复
				$yz=Db::name('attr_detail')->where('attrid',$attrid)->where('catid',$catid)->where('title',$param['title'])->count();
				if($yz){
					$this->error('选项名称重复了！');
				}else{
					$data['catid']=$catid;
					$data['attrid']=$attrid;
					$data['title']=$title;
					$res=Db::name('attr_detail')->insert($data);
					if($res){
						$this->success('增加选项成功');
					}else{
						$this->error('增加选项失败');
					}
				}
			}else{
				$catinfo=Db::name('category')->where('catid',$catid)->find();
				$attrinfo=Db::name('attr')->where('id',$attrid)->find();
				$nowlist=Db::name('attr_detail')->where('attrid',$attrid)->where('catid',$catid)->select();
				$this->assign('nowlist',$nowlist);
				$this->assign('catinfo',$catinfo);
				$this->assign('attrinfo',$attrinfo);
				return $this->fetch();
			}
		}
	}
?>