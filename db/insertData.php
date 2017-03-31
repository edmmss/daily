<?php 
public function sendMail($dataAry)
{
    $num = 500;
    $count = count($dataAry);

    //这是指定角色名发送邮件
    $startSql = " INSERT INTO {$this->dbGameName}.mail(uid, from_player_id, from_player_name, to_player_id, to_player_name,
    type, status, title, content, had_attachment, attachment_moneys, award_type, mail_dt) VALUES ";

    if ($count > $num) 
    {
        //超过指定数量时（分批发送）
        $i = 1;
        foreach ($dataAry as $v) 
        {
            $endSql .= " ('".uuid()."', -1, '系统', '{$v['pid']}', '{$v['name']}', 1, 2, '{$v['title']}', 
            '{$v['contents']}', 2, '{$v['money']}', 1, NOW()),";

            //当$endSql循环到 $num 次就发送一次sql
            if ($i % $num == 0) 
            {
                $res = $this->slaveDb->exec($startSql.rtrim($endSql, ','));
                //这里清空字符串
                $endSql = '';
            }
            $i++;
        }
        //把最后剩余的插入数据库
        $res = $this->slaveDb->exec($startSql.rtrim($endSql, ','));

    } 
    else 
    {
        //没有超过指定数量
        foreach ($dataAry as $v) 
        {
            $endSql .= " ('".uuid()."', -1, '系统', '{$v['pid']}', '{$v['name']}', 1, 2, '{$v['title']}', 
            '{$v['contents']}', 2, '{$v['money']}', 1, NOW()),";
        }

        $res = $this->slaveDb->exec($startSql.rtrim($endSql, ','));
    }

    if ($res) 
    {
        //成功返回
        return getReturn($res, 5);
    }

    return getReturn('', 3);
    
}
