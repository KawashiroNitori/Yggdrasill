<?php
function build_return($err_str, $other_args = NULL)
{
    $_ERROR_MSG=array
    (
        'OK'=>array('errcode'=>0, 'errmsg'=>'OK'),
        'unknown_command'=>array('errcode'=>1, 'errmsg'=>'未知指令'),
        'access_denied'=>array('errcode'=>2, 'errmsg'=>'拒绝访问'),
        'illegal_msg'=>array('errcode'=>3, 'errmsg'=>'非法数据'),
        'no_data'=>array('errcode'=>10000, 'errmsg'=>'无数据'),
        'group_not_exist'=>array('errcode'=>10001, 'errmsg'=>'群组不存在'),
        'error_username_or_password'=>array('errcode'=>29992, 'errmsg'=>'用户名或密码错误。'),
        'empty_password'=>array('errcode'=>29993, 'errmsg'=>'密码不能为空。'),
        'empty_username'=>array('errcode'=>29994, 'errmsg'=>'用户名不能为空。'),
        'duplicate_username'=>array('errcode'=>29995, 'errmsg'=>'用户名已被注册。'),
        'invalid_regcode'=>array('errcode'=>29996, 'errmsg'=>'注册码无效。'),
        'error_regcode'=>array('errcode'=>29997, 'errmsg'=>'注册码错误。'),
        'too_short_password'=>array('errcode'=>29998, 'errmsg'=>'密码太短。'),
        'too_short_username'=>array('errcode'=>29999, 'errmsg'=>'用户名太短。'),
        'no_running_episode'=>array('errcode'=>39996, 'errmsg'=>'章节未开始'),
        'no_ended_episode'=>array('errcode'=>39997, 'errmsg'=>'章节未结束'),
        'no_rollback_msg'=>array('errcode'=>39998, 'errmsg'=>'无发言'),
        'timeout_rollback'=>array('errcode'=>39999, 'errmsg'=>'超过撤回时限'),
        'verify_fail'=>array('errcode'=>40001, 'errmsg'=>'身份验证失败'),
        'invalid_time'=>array('errcode'=>40002, 'errmsg'=>'系统时间不正确')
    );
    
    //echo $err_str.PHP_EOL.$_ERROR_MSG[$err_str];
    if (isset($_ERROR_MSG[$err_str]))
        $returnArr=$_ERROR_MSG[$err_str];
    else
        return NULL;
    
    if ($other_args)
    {
        foreach($other_args as $key => $value)
        {
            $returnArr[$key]=$value;
        }
    }
    return json_encode($returnArr);
}

?>
