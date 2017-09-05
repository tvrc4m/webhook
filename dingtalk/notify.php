<?php

class DingtalkNotify {

    /**
     * 钉钉通知地址
     * @var string
     */
    private $notify_url="https://oapi.dingtalk.com/robot/send";

    /**
     * token
     * @var string
     */
    protected $access_token;

    public function __construct($access_token){

        $this->access_token=$access_token;

        if(empty($this->access_token)) exit("Dingtalk's robot access_token is missing");
    }
    /**
     * 创建jira issue的通知
     * @param  string $title    
     * @param  string $url      
     * @param  string $assignee 
     * @param  array $priority  
     * @return 
     */
    public function issueCreate(JiraMessage $jira_message){

        $issue=$jira_message->getIssueNumber();
        $title=$jira_message->getIssueTitle();
        $summary=$jira_message->getIssueSummary();
        $priority=$jira_message->getIssuePriority();
        $assignee=$jira_message->getIssueAssigneePhone();
        $creator=$jira_message->getIssueCreator();
        $url=$jira_message->getIssueUrl();

        $text="> {$creator}新建任务: {$issue}\n\n> [{$title}]($url)\n\n > 优先级: ![]({$priority['icon']}){$priority['name']}\n\n> 开发: @{$assignee}";

        $data=$this->markdown($creator.'新建任务: '.$issue,$text,[$assignee]);
        // print_r($data);
        $resp=$this->http($data);

        return $resp;
    }

    /**
     * issue update
     * @param  JiraMessage $jira_message 
     * @return 
     */
    public function issueUpdated(JiraMessage $jira_message){

        $title=$jira_message->getIssueTitle();
        $summary=$jira_message->getIssueSummary();
        $priority=$jira_message->getIssuePriority();
        $assignee=$jira_message->getIssueAssigneePhone();
        $url=$jira_message->getIssueUrl();

        $text="> {$title}\n\n> ![]({$priority['icon']}){$priority['name']}\n\n###查看[Jira]({$url})";

        $data=$this->markdown($title,$text,[$assignee]);

        $resp=$this->http($data);
    }

    /**
     * Issue has resolved.should notify tester to test
     * @param  JiraMessage $jira_message 
     * @return 
     */
    public function issueResolved(JiraMessage $jira_message){

        $issue=$jira_message->getIssueNumber();
        $title=$jira_message->getIssueTitle();
        $priority=$jira_message->getIssuePriority();
        $assignee=$jira_message->getIssueAssigneePhone();
        $url=$jira_message->getIssueUrl();
        $project=$jira_message->getProject();
        $groups=$jira_message->getGroups($project);

        $notify_users=[];
        $list_users=[$assignee];

        foreach ($groups['php']['test'] as $user){

            $notify_users[]='@'.$user;
            $list_users[]=$user;  
        } 

        $notify_users_list=implode(' ', $notify_users)

        $text="> @{$assignee} 解决了任务: {$issue}\n\n> [{$title}]({$url})\n\n> 优先级: ![]({$priority['icon']}){$priority['name']}\n\n> {$notify_users_list}";

        $data=$this->markdown($assignee.'解决了任务: '.$issue,$text,$list_users);

        $resp=$this->http($data);
    }

    /**
     * Issue has reopened.should notify the developer
     * @param  JiraMessage $jira_message 
     * @return 
     */
    public function issueReopened(JiraMessage $jira_message){

        $issue=$jira_message->getIssueNumber();
        $title=$jira_message->getIssueTitle();
        $priority=$jira_message->getIssuePriority();
        $assignee=$jira_message->getIssueAssigneePhone();
        $url=$jira_message->getIssueUrl();
        $operator=$jira_message->getIssueOperator();

        $text="> {$operator} 重新打开任务: {$issue}\n\n> [{$title}]({$url})\n\n> 优先级: ![]({$priority['icon']}){$priority['name']}\n\n> 开发: @{$assignee}";
        
        $data=$this->markdown('Reopen任务: '.$issue,$text,[$assignee]);

        $resp=$this->http($data);
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
            'at'=>['atMobiles'=>$assignee,'isAtAll'=>false]
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
            'at'=>['atMobiles'=>$assignee,'isAtAll'=>false]
        ];
    }

    private function http($data){

        $ch=curl_init();

        curl_setopt($ch,CURLOPT_TIMEOUT,60);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json;charset=utf-8']);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);
        print_r(json_encode($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($ch, CURLOPT_URL, $this->notify_url."?access_token=".$this->access_token);

        if( ! $result = curl_exec($ch)) {
            $error=curl_error($ch);
        }
        print_r($result);
        curl_close($ch);
        @file_put_contents('/tmp/jira.log', var_export($result,true).PHP_EOL,FILE_APPEND);
        return $result;
    }
}