<?php

namespace app\index\model;

use \think\Model;
use app\index\controller\WeChat;
use \think\Db;
/**
* 参赛选手模型
*/
class Player extends Model
{
	/**
	 * vote_update 票数更新
	 * @date        2017-11-22
	 * @param       int                   $player_id 选手标识
	 * @param       string                  $open_id   description
	 */
	public function vote_update($player_id, $open_id)
	{
		$voter = new Voter;
		$wx = new WeChat;
		$date = date('Y-m-d');
		// 判断投票者open_id是否有效,若有效则判断是否投票
        // -1代表open_id无效 0代表已经达到本日投票上限 1代表投票成功
        $endTime = strtotime("2018-4-20 00:00:00");
        if ($endTime <= time()){
            $data = array("status_code" => -1);
        }else if ($wx->index('checkId', $open_id)) {
			if($voter->isVote($open_id, $date)){
				$data = array("status_code" => 0);
			}else{
				$this->where('player_id', $player_id)->setInc('voter_number');
				$data = ['open_id' => $open_id, 'vote_player_id' => $player_id, 'last_vote_date' => $date];
				$voter->insert($data);
				$data = array("status_code" => 1);
			}
		}else{
            $data = array("status_code" => -1);
        }
		print_r(json_encode($data));
	}

	/**
	 * getMsg 获取选手信息
	 * @date        2017-11-23
	 * @return      array    $this->all()           json格式的选手信息
     * @throws      $e
	 */
	public function getMsg()
	{
        try {
            return $this->all();
        }
        catch (mysqli_sql_exception $e){
            return 0;
        }
	}

}

?>