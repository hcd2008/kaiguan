<?php
	namespace app\common\controller;
	use think\Controller;
	use think\Session;
	use think\Db;
	use think\Config;

	class Base extends Controller{
		protected $qx;
		/**
		 * 基本类判断用户登录等
		 * @Author    黄传东
		 * @DateTime  2017-03-15T14:30:03+0800
		 * @copyright 风险评估中心信息平台
		 */
		public function __construct(){
			parent::__construct();

		}
		public function _initialize(){
			$this->isLogin();
			$this->quanxian();
			$this->assign('controller',$this->request->controller());
			$this->assign('qx',$this->qx);
			// print_r($this->qx);
		}
		/**
		 * 判断是否已登录
		 * @Author    黄传东
		 * @DateTime  2017-03-15T14:22:06+0800
		 * @copyright 风险评估中心信息平台
		 * @return    boolean                  [description]
		 */
		public function isLogin(){
			if(!Session::has('uid')){
				$this->redirect("login/index");
			}
		}
		/**
		 * 权限判断
		 * @Author   黄传东
		 * @DateTime 2017-04-10T13:37:35+0800
		 * @return   [type]                   [description]
		 */
		public function quanxian(){
			$roleid=Session::get('roleid');
			$info=Db::name('role')->where('id',$roleid)->find();
			$state=$info['status'];
			if($state!=1){
				$this->error('您的用户组已禁用，请联系管理员','login/index');
			}
			$qx=$info['quanxian'];
			$qxconf=Config::get('quanxian');
			$qxcontroller=Config::get('qxcontroller');
			$qxarr=explode(",", $qx);
			$ctarr=array();
			foreach($qxarr as $k=>$v){
				$ctarr[]=$qxcontroller[$v];
			}
			$this->qx=$ctarr;
			$kzq=$this->request->controller();
			if($kzq!='Index'&& $kzq!='Comment'){
				if(!in_array($kzq, $ctarr)){
					$this->error('没有权限,请联系管理员');
				}
			}
			
		}
	}
?>