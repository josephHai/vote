<?php

namespace app\index\model;

use \think\Model;
use \think\Db;

/**
* 参赛选手模型
*/
class Player extends Model
{

	/**
	 * add 添加选手信息
	 * @date        2017-11-22
	 * @param       array                   $arr post数据
	 */
	public function add($arr)
	{
		$this->player_name = $arr['player_name'];
		$this->pic = $arr['pic'];
		$this->message = $arr['message'];
		$this->declaration = $arr['declaration'];
		$this->save();
		$msg = array("status_code" => 1, "msg" => "添加成功");
		print_r(json_encode($msg));
	}

	/**
	 * cut 删除选手信息
	 * @date        2017-11-22
	 * @param       array                   $arr post数据
     * @throws
	 */
	public function cut($arr)
	{
		if ($this::get($arr['player_id'])) {
			$this::destroy($arr['player_id']);
			$msg = array("status_code" => 1, "msg" => "删除成功");	
		}else{
			$msg = array("status_code" => -2, "msg" => "要删除的数据不存在");
		}
		print_r(json_encode($msg));
	}

	/**
	 * change 修改选手信息
	 * @date        2017-11-23
	 * @param       array                   $arr post数据
	 */
	public function change($arr)
	{
		if ($this::get($arr['player_id'])) {
			$this->update($arr);
			$msg = array("status_code" => 1, "msg" => "修改成功");	
		}else{
			$msg = array("status_code" => -2, "msg" => "要修改的数据不存在");
		}
		print_r(json_encode($msg));
	}

	/**
	 * getMsg 获取选手信息
	 * @date        2017-11-23
	 * @return      string                   json格式的选手信息
	 */
	public function getMsg()
	{
		$msg = $this->all();
		$msg = json_encode($msg);
		print_r(json_encode($msg));
	}

	public function playerMsg()
    {
        $sql1 = "SELECT player_name, voter_number, player_id, COUNT(vote_player_id) date1_vote_num FROM think_player, think_voter
                WHERE think_player.player_id = think_voter.vote_player_id AND  last_vote_date = '2018-4-18' GROUP BY vote_player_id";
        $sql2 = "SELECT COUNT(vote_player_id) date2_vote_num FROM think_voter WHERE last_vote_date = '2018-4-19' GROUP BY vote_player_id";
        $arr1 = Db::query($sql1);
        $arr2 = Db::query($sql2);
        for ($i = 0; $i < count($arr1); $i++){
            $arr1[$i]['date2_vote_num'] = $arr2[$i]['date2_vote_num'];
        }
        return $arr1;
    }

}

?>