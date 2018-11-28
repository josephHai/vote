<?php
/**
 * Created by PhpStorm.
 * User: ctHai
 * Date: 2018/3/18
 * Time: 16:16
 */
namespace app\index\Model;

use \think\Model;
class Voter extends Model
{
    /**
     * isVote    判断用户是否已投票
     *
     * @param    string    $open_id    用户的唯一标识符
     * @param    Date      $date       此时的日期
     * @return    bool     true表示已投票，false表明未投票
     * @throws
     */
    public function isVote($open_id, $date)
    {
        $result = $this->where('open_id', $open_id)->select();
        if (sizeof($result) != 0){
            $item = end($result);
            $last_vote_date = $item['last_vote_date'];
            if ($last_vote_date == $date){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

}
?>