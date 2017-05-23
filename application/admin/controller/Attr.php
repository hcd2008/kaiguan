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
			$res=Db::name('attr')->order('paixu,id')->select();
			$this->assign('lists',$res);
			return $this->fetch();
		}
		/**
		 * 编辑属性
		 * @Author   hcd
		 * @DateTime 2017-05-19T23:01:45+0800
		 * @version  [version]
		 * @return   [type]                   [description]
		 */
		public function editAttr(){
			if($this->request->isPost()){
				isset($this->param['attr_cn']) or $this->error('属性中文名不能为空');
				isset($this->param['paixu']) or $this->error('排序不能为空');
				$id=$this->param['id'];
				$res=Db::name('attr')->where('id',$id)->update(['attr_cn'=>$this->param['attr_cn'],'paixu'=>$this->param['paixu']]);
				if($res){
					$this->success('编辑成功');
				}else{
					$this->error('编辑失败');
				}
			}else{
				$res=Db::name('attr')->order('id')->select();
				$this->assign('lists',$res);
				return $this->fetch();
			}
		}

		/**
		 * 添加属性
		 * @Author   hcd
		 * @DateTime 2017-05-19T23:16:51+0800
		 * @version  [version]
		 */
		public function addAttr(){
			if($this->request->isPost()){
				$attr=isset($this->param['attr'])?trim($this->param['attr']):'';
				if($attr=='') $this->error('字段名不能为空');
				$this->param['attr_cn']!='' or $this->error('中文名不能为空');
				$chang=strlen($this->param['attr']);
				if($chang<=4||$chang>=6){
					$this->error('字段名长度为4-6位');
				}
			}else{
				return $this->fetch();
			}
		}
	}
?>
