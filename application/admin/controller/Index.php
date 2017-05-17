<?php
	namespace app\admin\controller;
	use app\common\controller\AdminBase;

	class Index extends Adminbase{
		public function index(){
			return $this->fetch();
		}
	}
?>