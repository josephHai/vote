<?php

namespace app\index\controller;

use app\index\model\Player;
use \think\Controller;
use app\index\model\AdminModel;
use app\index\model\Voter;

/**
* 后台管理
*/
class Admin extends Controller
{

	/**
	 * 构造函数
	 */
	function __construct()
	{
		if (!session('?ext_user')) {
			header(strtolower("location: login"));
			exit();
		}
		parent::__construct();
		$this->player = new Player();
		$this->data = $_POST;
		$this->voter = new Voter();
	}
	
	/**
	 * admin 渲染后台管理界面
	 * @date        2017-11-23
	 * @return      {func}                   渲染界面
	 */
	public function admin()
	{
		return $this->fetch('playerMsg');
	}

	/**
	 * changepsw 渲染修改密码界面
	 * @date        2017-11-23
	 * @return      {fun}                   渲染界面
	 */
	public function changepsw()
	{
	    return $this->fetch();
	}

	/**
	 * logout 退出登陆
	 * @date        2017-11-23
	 * @return      {char}                   NULL
	 */
	public function logout()
	{
		$admin = new AdminModel;
		$admin->logout();
		$this->redirect(config('web') . 'login');

		return NULL;
	}
	
	/**
	 * add 添加选手信息
	 * @date        2017-11-22
	 */
	public function add()
	{
		$result = $this->validate($this->data, "Player.add");
		if (true !== $result) {
			$error_msg = array("status_code" => -1, "error_msg" => $result);
			return json_encode($error_msg);
		}else{
			$this->player->add($this->data);
		}
	}

	/**
	 * cut 删除选手信息
	 * @date        2017-11-22
	 * @anotherdate 2017-11-22T21:20:48+0800
	 * @return      type                   description
	 */
	public function cut()
	{
		$result = $this->validate($this->data, "Player.cut");
		if (true !== $result) {
			$error_msg = array("status_code" => -1, "error_msg" => $result);
			return json_encode($error_msg);
		}else{
			$this->player->cut($this->data);
		}
	}

	/**
	 * change 修改选手信息
	 * @date        2017-11-22
	 */
	public function change()
	{
		$result = $this->validate($this->data, "Player.change");
		if (true !== $result) {
			$error_msg = array("status_code" => -1, "error_msg" => $result);
			return json_encode($error_msg);
		}else{
			$this->player->change($this->data);
		}
	}

	/**
	 * getMsg 获取选手信息
	 * @date        2017-11-23
	 * @return      {json}                  json格式的选手信息
	 */
	public function getMsg()
	{
		return $this->player->getMsg();
	}

	/**
	 * [upload 图片上传]
	 * @date        2017-12-04
	 */
	public function upload()
	{
		$file = request()->file('file');
		if ($file) {
			$info = $file->validate(["size"=>2048000,"ext"=>"jpg,png,gif"])->move("images/");
			if ($info) {
	            // 成功上传后 获取图片路径
	           	$data = array("status_code" => 1, "url" => "/admin/images/".$info->getSaveName());
	            print_r(json_encode($data));
			}else{
	            // 上传失败获取错误信息
	            $result = $file->getError();
				$error_msg = array("status_code" => -3, "error_msg" => $result);
				return json_encode($error_msg);
			}
		}else{
			$error_msg = array("status_code" => -1, "error_msg" => '参数错误!');
			return json_encode($error_msg);
		}
	}

	public function voteMsg()
	{
        $data = $this->voter->voteMsg($_GET);
		return json_encode($data);
	}

	public function playerMsg()
	{
        $data = $this->player->playerMsg();
        return json_encode($data);
	}
}

?>
