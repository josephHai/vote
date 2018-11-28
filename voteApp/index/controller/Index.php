<?php

namespace app\index\controller;

use app\index\model\Player;
use app\index\model\Voter;
use think\Exception;
/**
*参赛选手控制器
*/
class Index extends \think\Controller
{

    /**
     * index    入口文件
     *
     */
    public function index()
    {
        $voter = new Voter;
        try {
            $_GET['id'];
        }catch (Exception $e){
            return $this->fetch('notWx');
        }
        $open_id = $_GET['id'];
        $list = $this->getMsg();
        $this->assign('list', $list);
        $date = date('Y-m-d');
        $endTime = strtotime("2018-4-20 00:00:00");
        if ($endTime <= time()){
        	return $this->fetch('stopVoting');
        }
        if ($voter->isVote($open_id, $date)){
            return $this->fetch('hasVoting');
        }else{
            return $this->fetch('notVoting');
        }
    }
	
	/**
	 * getMsg 获取选手信息
	 * @date        2017-11-23
	 * @return      {json}                   json格式的选手信息
	 */
	private function getMsg()
	{
		$player = new Player;
		return $player->getMsg();
	}

	/**
	 * vote 用户投票
	 * @date        2017-11-22
	 */
	public function vote()
	{
		$player_id = $_POST['playerId'];
		$open_id = $_POST['openId'];
		$player = new Player;
		$player->vote_update($player_id, $open_id);
	}
}

?>