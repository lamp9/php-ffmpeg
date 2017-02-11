<?php
class LoginAction extends Action {
	
	public function index(){
		$this->display();
	}
	
	public function login(){
		if($_SESSION['code'] != md5($_POST['code'])) LoginAction::logout();
		$data		= return_login_data();
		$name		= $this->_post('name');
		$data_user	= $data[$name];
		$this->_post('pwd');
		if(
			$data_user['pwd']	== md5($this->_post('pwd')) &&
			$data_user['allow']	== 'T'
		){
			session('id', $data_user['id']);
			session('name', $name);
			$this->redirect('Index/index');
		} else LoginAction::logout();
	}
	
	public function login_auto(){
		$data		= return_login_data();

		$name		= $this->_post('name');
		$data_user	= $data[$name];
		if(
			$data_user['pwd']	== md5($this->_post('pwd')) &&
			$data_user['allow']	== 'T'
		){
			session('id', $data_user['id']);
			session('name', $name);
			$this->redirect('Index/index');
		} else LoginAction::logout();
	}
	
	public function logout(){
		session('[destroy]');
		$this->redirect('Login/index');
	}
	
	public function logout_for_main(){
		session('[destroy]');
		echo "<script>";
		echo "window.close();";
		echo "</script>";
	}
	
	public function verify(){
		import('ORG.Util.Image');
		Image::buildImageVerify(4, 1, "png", 50, 25, "code");
	}
}