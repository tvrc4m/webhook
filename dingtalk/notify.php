<?php

class DingtalkNotify {

    const HTTP_METHOD_GET='get';
    const HTTP_METHOD_POST='post';

    /**
     * 钉钉通知地址
     * @var string
     */
    protected $notifyUrl="https://oapi.dingtalk.com/robot/send?access_token=865acac088fc8dacf5ca42fefbb56380b38d16d96f5bbcf50fbf6961a31f2016";

    public function __construct($url=''){

        !empty($url) && $this->notifyUrl=$url;
    }
    /**
     * 创建jira issue的通知
     * @param  string $title    
     * @param  string $url      
     * @param  string $assignee 
     * @param  array $priority  
     * @return 
     */
    public function issueCreate($title,$summary,$url,$assignee,$priority){

        if(empty($title)) return false;

        $text="> {$summary}\n> {$priority['name']}\n### 查看[Jira]({$url})";

        $data=$this->text($title,"test",[$assignee]);
        // print_r($data);
        $resp=$this->http($this->notifyUrl,$data);

        return $resp;
    }

    public function issueUpdated($title,$summary,$url,$assignee,$priority){

        if(empty($title)) return false;

        $text="> {$summary}\n> ![]({$priority['icon']}){$priority['name']}\n###查看[Jira]({$url})";

        $data=$this->text($title,$text,[$assignee]);

        $resp=$this->http($this->notifyUrl,$data);
    }


    private function notify($msgtype){

        switch ($msgtype) {
            case 'markdown':$this->markdown($msgtype);
                break;
            
            default:
                # code...
                break;
        }
    }

    private function text($title,$text,$assignee){

        return 
        [
            'msgtype'=>'text',
            'text'=>['content'=>$text],
            // 'at'=>['atMobiles'=>$assignee,'isAtAll'=>false]
        ];
    }

    /**
     * 以markdown格式传输
     * @param  string $title    
     * @param  string $text     
     * @param  array $assignee 
     * @return 
     */
    private function markdown($title,$text,$assignee){

        return 
        [
            'msgtype'=>'markdown',
            'markdown'=>['title'=>$title,'text'=>$text],
            'at'=>['atMobiles'=>['15763951212'],'isAtAll'=>false]
        ];
    }

    private function http($url,$data){

        $ch=curl_init();

        curl_setopt($ch,CURLOPT_TIMEOUT,60);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json;charset=utf-8']);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);
        print_r(json_encode($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_URL, $url);

        if( ! $result = curl_exec($ch)) {
            $error=curl_error($ch);
        }
        // print_r($result);
        curl_close($ch);

        return $result;
    }
}