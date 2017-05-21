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
		/**
		 * 编辑分类
		 * @Author   黄传东
		 * @DateTime 2017-05-17T17:09:01+0800
		 * @return   [type]                   [description]
		 */
		public function editCategory(){
			if($this->request->isPost()){
				$catname=isset($this->param['catname'])?trim($this->param['catname']):'';
				isset($this->param['catid']) or $this->error('非法访问');
				if($catname=='') $this->error('分类名不能为空');
				//检测标题是否重复
				$arr['catid']=$this->param['catid'];
				$arr['catname']=$this->param['catname'];
				$arr['catid']=['<>',$this->param['catid']];
				$jc=Db::name('category')->where($arr)->count();
				if($jc){
					$this->error('该分类下已经有相同名称分类');
				}else{
					$res=Db::name('category')->update($this->param);
					if($res){
						$this->success('更新成功');
					}else{
						$this->success('更新失败');
					}	
				}
			}else{
				isset($this->param['catid']) or $this->error('非法访问');
				$info=Db::name('category')->where('catid',$this->param['catid'])->find();
				$this->assign('info',$info);
				return $this->fetch();
			}
		}
		/**
		 * 添加子分类
		 * @Author   黄传东
		 * @DateTime 2017-05-18T09:06:30+0800
		 */
		public function addSon(){
			$parentid=isset($this->param['catid'])?$this->param['catid']:0;
			if($this->request->isPost()){
				trim($this->param['catname'])!='' or $this->error('请填写分类名');
				trim($this->param['paixu'])!='' or $this->error('请填写排序');
				//判断是否重复
				$arr['catname']=$this->param['catname'];
				$arr['parentid']=$parentid;
				$arr['status']=1;
				$cfarr=Db::name('category')->where($arr)->count();
				if($cfarr){
					$this->error('分类名称已存在');
				}
				
				unset($this->param['catid']);
				$this->param['parentid']=$parentid;
				
				$res=Db::name('category')->insert($this->param);
				if($res){
					$this->success('添加成功','category/index');
				}else{
					$this->error('添加失败');
				}
			}else{
				if($parentid){
					$parinfo=Db::name('category')->where('catid',$parentid)->find();
					$parname=$parinfo['catname'];
				}else{
					$parname=0;
				}
				$this->assign('parname',$parname);
				$this->assign('parentid',$parentid);
				return $this->fetch();
			}
		}
		/**
		 * 删除分类
		 * @Author   黄传东
		 * @DateTime 2017-05-18T10:48:13+0800
		 * @return   [type]                   [description]
		 */
		public function delCategory(){
			isset($this->param['catid'])  or $this->error('非法访问');
			$res=Db::name('category')->where('catid',$this->param['catid'])->update(['status'=>0]);
			if($res){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
	}
	
?>