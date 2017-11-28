<?php

define('ROOT', dirname(__dir__));

$access_token='699b16574ca2c3c851095fe5555fe5b878a5d96e4303d0a5ce8a1d991e3b1166';

include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$developers=['郑生齐','肖阿勇','王娟娟','李硕','刘艳梅','刘宏富','武超','白宝强','许俊帅','孔彬','魏山'];

$week=['星期一','星期二','星期三','星期四','星期五'];

while (1) {
    
    shuffle($developers);

    $slice=array_chunk($developers, 2);

    $ok=1;

    foreach ($slice as $arr) {
    
        if($arr[0]=='刘宏富' && $arr[1]=='孔彬'){

            $ok=0;

            break;
        }
    }

    if($ok){

        $text="1. 如果当前有人加班,则当天值班可取消,理论不不少于2人,特殊情况下除外.\n2. 针对个人实际情况,可相互间互换调整.\n3. 晚上提供晚餐,可选择自助,20元标准,依旧走订餐流程.\n4. 本次值班由程序生成,值不值班全靠运气.\n";

        foreach ($week as $index=>$day) {

            $text.="> {$day}:{$slice[$index][0]},{$slice[$index][1]}.\n\n";
        }

        $exclude=[];
        
        for ($i = 5; $i < count($slice); $i++) {

            if(count($slice[$i])==1)
                $exclude[]=$slice[$i][0];
            else{
                $exclude[]=$slice[$i][0];  
                $exclude[]=$slice[$i][1];  
            } 
        }
        
        $text.="恭喜".implode(',', $exclude)."下周没有排上";
        
        $dingtalk_notify->notifyAll('下周PHP值班安排计划',$text);

        exit;
    }
}

