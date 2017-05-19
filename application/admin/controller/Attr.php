<?php
	namespace app\admin\controller;
	use app\common\controller\AdminBase;
	use think\Db;

	class Attr extends AdminBase{
		/**
		 * 属性列表
		 * @Author   黄传东
		 * @DateTime 2017-05-18T11:37:54+0800
		 * @return   [type]                   [description]
		 */
		public function index(){
			$res=Db::name('attr')->order('id')->select();
			$this->assign('lists',$res);
			return $this->fetch();
		}

		public function editAttr(){
			if($this->request->isPost()){

			}else{
				isset($this->param['id']) or $this->error('非法访问');
				$info=Db::name('attr')->where('id',$this->param['id'])->find();
				$this->assign("info",$info);
				return $this->fetch();
			}
		}
	}
?>