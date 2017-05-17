<?php
	namespace app\admin\controller;
	use app\common\controller\AdminBase;
	use think\Db;
	use think\Validate;
	use think\Config;
	class Group extends Adminbase{
		/**
		 * 用户组管理，用来设置权限
		 * @Author    黄传东
		 * @DateTime  2017-03-16T09:35:48+0800
		 * @copyright 风险评估中心信息平台
		 * @return    [type]                   [description]
		 */
		public function index(){
			$res=Db::name("role")->order("sort")->select();
			$qxconf=Config::get('quanxian');
			$this->assign("qxconf",$qxconf);
			$this->assign("lists",$res);
			return $this->fetch();
		}
		/**
		 * 添加用户组
		 * @Author    黄传东
		 * @DateTime  2017-03-16T09:46:46+0800
		 * @copyright 风险评估中心信息平台
		 */
		public function addGroup(){
			if($this->request->isPost()){
				$param=$this->request->param();
				unset($param['submit']);
				$rule=[
					'name'=>'require',
					'quanxian'=>'require',
					'sort'=>'require'
				];
				$msg=[
					'name.require'=>'名称必须',
					'quanxian.require'=>'请选择权限',
					'sort'=>'请填写排序'
				];
				$validate=new Validate($rule,$msg);
				if(!$validate->check($param)){
					$this->error($validate->getError());
				}
				if($this->checkGroup($param['name'])){
					$this->error("角色名重复,请更换角色名");
					exit;
				}

				$param['addtime']=time();
				$param['quanxian']=implode($param['quanxian'], ",");

				$res=Db::name('role')->insert($param);
				if($res){
					$this->success('添加成功','group/index');
				}else{
					$this->error('添加失败');
				}
			}else{
				$qxconf=Config::get('quanxian');
				$this->assign("qxconf",$qxconf);
				return $this->fetch();
			}
			
		}
		/**
		 * 编辑角色
		 * @Author    黄传东
		 * @DateTime  2017-03-20T09:59:00+0800
		 * @copyright 风险评估中心信息平台
		 * @return    [type]                   [description]
		 */
		public function editGroup(){
			if($this->request->isPost()){
				$param=$this->request->param();
				$param['quanxian']=implode(",",$param['quanxian']);
				$rule=[
					'name'=>'require',
					'quanxian'=>'require',
					'sort'=>'require'
				];
				$msg=[
					'name.require'=>'名称必须',
					'quanxian.require'=>'请选择权限',
					'sort'=>'请填写排序'
				];
				$validate=new Validate($rule,$msg);
				if(!$validate->check($param)){
					$this->error($validate->getError());
				}
				//判断名称是否重复
				if($this->checkGroup($param['name'],$param['id'])){
					$this->error('角色名重复,请更换角色名');
				}
				
				$res=Db::name('role')->update($param);
				if($res){
					$this->success("编辑成功","group/index");
				}else{
					$this->error("编辑失败");
				}
			}else{
				$param=$this->request->param();
				$id=isset($param['id'])?intval($param['id']):0;
				if($id==0){
					$this->error("非法访问");
					exit;
				}
				$res=Db::name('role')->where('id',$id)->find();
				$quanxian=$res['quanxian'];
				$quanxian=explode(",",$quanxian);
				$this->assign("quanxian",$quanxian);
				$this->assign("data",$res);
				$qxconf=Config::get('quanxian');
				$this->assign("qxconf",$qxconf);
				return $this->fetch();
			}	
		}
		/**
		 * 删除角色
		 * @Author    黄传东
		 * @DateTime  2017-03-21T10:03:22+0800
		 * @copyright 风险评估中心信息平台
		 * @return    [type]                   [description]
		 */
		public function delGroup(){

		}
		/**
		 * 检测是否已添加该角色名
		 * @Author    黄传东
		 * @DateTime  2017-03-21T10:04:21+0800
		 * @copyright 风险评估中心信息平台
		 * @return    [type]                   [description]
		 */
		public function checkGroup($name,$id=0){
			if($id){
				$res=Db::name('role')->where('name',$name)->where('id','<>',$id)->count();
			}else{
				$res=Db::name('role')->where('name',$name)->count();
			}
			return $res;
		}
	}
?> 