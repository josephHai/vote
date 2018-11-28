<?php
/**
 * Created by PhpStorm.
 * User: junya
 * Date: 2018/4/18
 * Time: 12:13
 */

namespace app\index\model;

use \think\Model;
use \think\Db;
class Voter extends Model
{
    public function voteMsg($arr)
    {
        if ($arr === null || count($arr) === 0){
            $sql = 'SELECT open_id, vote_player_id, last_vote_date FROM think_voter WHERE 1';
        }elseif ($arr['id'] && !isset($arr['page'])){
            $id = $arr['id'];
            $sql = 'SELECT id FROM think_voter WHERE vote_player_id='.$id;
        } else{
            $id = $arr['id'];
            $page = $arr['page'];
            $pre = ($page-1)*10;
            $sql = 'SELECT nickname, last_vote_date FROM think_voter_msg, think_voter WHERE think_voter_msg.id = think_voter.id AND vote_player_id = '.$id.' limit '.$pre.', 10';
        }
        return Db::query($sql);
    }
}