<?php
class ToolsAction extends Action {
	
	//登录检测
	public function login_check(){
		if('' == session('id') || false == session('id')) LoginAction::logout();
	}
	
	public function return_url(){
		echo "<script>";
		echo "location.href='".cookie('video_url')."'";
		echo "</script>";
	}
	
	public function return_page($table, $data, $page_type, $header, $num){
		import('ORG.Util.'.$page_type);
		if($data)
			$arr = $table->where($data)->select();
		else
			$arr = $table->select();
		$count=count($arr);
		$page = new Page($count,$num);
		$page->setConfig('header',$header);
		return $page;
	}
	//根据分页信息返回数据记录
	public function return_result($table, $data, $relation, $order, $page){
		if($data) $table = $table->where($data);
		if($order) $table = $table->order($order);
		if($relation) $table = $table->relation(true);
		return $table->limit($page->firstRow.','.$page->listRows)->select();
	}
	
	public function result_all($table, $data, $relation, $order){
		if($data) $table = $table->where($data);
		if($order) $table = $table->order($order);
		if($relation) $table = $table->relation(true);
		return $table->select();
	}
	
}