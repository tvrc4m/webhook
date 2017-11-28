<?php

class DingtalkNotify {

    private $webhook_url="http://webhook.vrcdkj.cn";

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

        $text=" {$creator} 新建任务: {$issue}\n\n> [{$title}]($url)\n\n > 优先级: {$priority['name']}\n\n开发: @{$assignee}";

        $data=$this->markdown($creator.' 新建任务: '.$issue,$text,[$assignee]);
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
        $priority=$jira_message->getIssuePriority();
        $assignee=$jira_message->getIssueAssigneePhone();
        $url=$jira_message->getIssueUrl();
        $issue=$jira_message->getIssueNumber();
        $logs=$jira_message->getChangeLogs();
        $attachfiles=$jira_message->getAttachFiles();

        if(empty($logs)) return false;

        $text="[{$title}]($url)\n\n";

        $text.="> 优先级：{$priority['name']}\n\n";

        foreach ($logs as $log) {
            
            $text.="> {$log}\n\n";
        }

        foreach ($attachfiles as $type=>$files) {
            
            if($type=='image'){

                foreach ($files as $file) {
                    
                    $text.="![](".$file.")\n\n";
                }
            }
        }

        $data=$this->markdown($issue."内容更改",$text,[$assignee]);

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
        $assignee=$jira_message->getIssueAssignee();
        $url=$jira_message->getIssueUrl();
        $project=$jira_message->getProject();
        $groups=$jira_message->getGroups($project);

        $notify_users=[];

        foreach ($groups['php']['test'] as $user) $notify_users[]='@'.$user;

        $notify_users_list=implode(' ', $notify_users);

        $text=" {$assignee} 解决了任务: {$issue}\n\n> [{$title}]({$url})\n\n> 优先级: {$priority['name']}\n\n{$notify_users_list}";

        $data=$this->markdown($assignee.' 解决了任务: '.$issue,$text,$groups['php']['test']);

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

        $text=" {$operator} 重新打开任务: {$issue}\n\n> [{$title}]({$url})\n\n> 优先级: {$priority['name']}\n\n开发: @{$assignee}";
        
        $data=$this->markdown('Reopen任务: '.$issue,$text,[$assignee]);

        $resp=$this->http($data);
    }

    /**
     * git push code
     * @param  GitlabMessage $gitlab_message 
     * @return 
     */
    public function gitPush(GitlabMessage $gitlab_message,$update_merge_develop=0,$update_merge_app=0){

        $project=$gitlab_message->getProject();
        $username=$gitlab_message->getUserName();
        $commits=$gitlab_message->getCommits();
        $filter=$gitlab_message->getFilterBranch();
        $branch=$gitlab_message->getBranchName();

        if(in_array($branch, $filter)) return false;

        if(empty($commits)) return false;

        $text=" {$username} 往 {$branch}分支 上传了代码\n\n";

        foreach ($commits as $k=>$commit) {
            
            if($k<3 && strpos($commit['message'], 'Merge branch')===false && strpos($commit['message'], 'Merge remote')===false)
                $text.=">[{$commit['message']}]({$commit['url']})\n\n";
        }

        $test=[
            'title'=>'部署到TEST环境',
            'actionURL'=>$this->webhook_url."/deploy.php?branch={$branch}&env=test&access_token={$this->access_token}"
        ];
        $dev=[
            'title'=>'部署DEV环境',
            'actionURL'=>$this->webhook_url."/deploy.php?branch={$branch}&env=dev&access_token={$this->access_token}"
        ];
        $staging=[
            'title'=>'部署STAGING环境',
            'actionURL'=>$this->webhook_url."/deploy.php?branch={$branch}&env=staging&access_token={$this->access_token}"
        ];

        $btns=[$test];

        // $test_url=$this->webhook_url."/deploy.php?branch={$branch}&env=test&access_token={$this->access_token}";

        // if(in_array($branch, ['develop','app'])){

            // $btns=[$dev,$staging];
        // }else if(strpos(strtoupper($branch), 'KF-')!==false){

            // $btns=[$test,$dev];
        // }else{

        //     $btns=[$test,$dev,$staging];
        // }

        if($update_merge_develop==1){

            $uniq=uniqid();

            $merge_btn=[
                'title'=>"合并到develop分支",
                'actionURL'=>$this->webhook_url."/merge.php?branch={$branch}&key={$uniq}&dest=develop&project=php&access_token={$this->access_token}"
            ];

            array_push($btns, $merge_btn);

            // 记录保存的key
            @file_put_contents('/var/log/click_develop_merged.log',$uniq.PHP_EOL,FILE_APPEND);

            $text.="> *!!只有确定改好的代码才允许合并到develop分支!!*";
        }

        if($update_merge_app==1){

            $uniq=uniqid();

            $merge_btn=[
                'title'=>"合并到app分支",
                'actionURL'=>$this->webhook_url."/merge.php?branch={$branch}&key={$uniq}&dest=app&project=php&access_token={$this->access_token}"
            ];

            array_push($btns, $merge_btn);

            // 记录保存的key
            @file_put_contents('/var/log/click_app_merged.log',$uniq.PHP_EOL,FILE_APPEND);
        }

        $data=$this->card("{$branch}分支代码更新",$text,$btns);

        return $this->http($data);

    }

    public function gitMerge(GitlabMessage $gitlab_message){

        $title=$gitlab_message->merge_title;
        $url=$gitlab_message->merge_web_url."/commits";

        switch ($gitlab_message->merge_status) {

            // case 'opened':
            //     $data=$this->single($title,"\t".$title.' 请求已创建,等待合并','查看详情',$url);
            //     break;
            case 'merged':
                $this->notifyTextUrl($title,"已合并\n点击查看提交记录和修改文件对比",$url,$gitlab_message->merge_image);
                break;        
        } 
    }

    /**
     * 请求合并分支
     * @param  gitlab_message $gitlab_message 
     * @return 
     */
    public function reqMerge(GitlabMessage $gitlab_message){

        $branch=$gitlab_message->getBranchName();

        $text="####{$branch}分支之前已合并到develop,现在有更新，如若需要重新合并到develop，请点击下面的链接";

        $uniq=uniqid();

        $merge_btn=[
            'title'=>"合并{$branch}到develop",
            'actionURL'=>$this->webhook_url."/merge.php?branch={$branch}&key={$uniq}&project=php&access_token={$this->access_token}"
        ];
        // 记录保存的key
        @file_put_contents('/var/log/click_merged.log',$uniq.PHP_EOL,FILE_APPEND);

        $data=$this->card("{$branch}分支代码更新,请求合并",$text,[$merge_btn]);

        return $this->http($data);
    }
    /**
     * 开始deploy的提示
     * @return 
     */
    public function deployStart($env,$branch){

        $text="正在{$env}环境上部署{$branch}分支,可能有30秒的延迟";

        $data=$this->text("部署分支",$text);

        return $this->http($data);
    }

    /**
     * deploy成功
     * @param  string $env    
     * @param  string $branch 
     * @return 
     */
    public function deploySuccess($env,$branch){

        $text="{$env}环境上已部署{$branch}分支";

        $data=$this->text("部署分支",$text);

        return $this->http($data);
    }

    /**
     * 脚本执行后钉钉提醒
     * @return 
     */
    public function scriptSuccess($ding_array){

        $text="{$ding_array['name']}脚本执行状况\n\n";
        $text.=">[脚本名称：{$ding_array['name']}]\n\n";
        $text.=">[脚本描述：{$ding_array['desc']}]\n\n";
        $text.=">[脚本执行时间：{$ding_array['datetime']}]\n\n";
        $text.=">[脚本耗时时间：{$ding_array['run_time']}]\n\n";
        $text.=">[脚本占用内存：{$ding_array['memory_occupied']}]\n\n";
        $text.=">[脚本执行状态：{$ding_array['stat']}]\n\n";

        //$text=" {$assignee} 解决了任务: {$issue}\n\n> [{$title}]({$url})\n\n> 优先级: {$priority['name']}\n\n{$notify_users_list}";
        //$data=$this->markdown($assignee.' 解决了任务: '.$issue,$text,$groups['php']['test']);
        $data=$this->markdown('定时任务',$text);
        return $resp=$this->http($data);
    }
    /**
     * 通知提醒@ALL
     * @param  string $title 
     * @param  string $text  
     * @return 
     */
    public function notifyAll($title,$text){

        $data=$this->markdown($title,$text,$assignee=[],true);

        return $this->http($data);
    }

    /**
     * 普通文本通知
     * @param  string $title 标题
     * @param  string $text  内容
     * @return 
     */
    public function notifyText($title,$text,$isall=false){

        $data=$this->text($title,$text,[],$isall);

        return $this->http($data);
    }

    /**
     * 文本带链接通知
     * @param  string $title     
     * @param  string $text      
     * @param  string $url       url链接 
     * @return 
     */
    public function notifyTextUrl($title,$text,$url,$image){

        $data=$this->link($title,$text,$url,$image);

        return $this->http($data);
    }
    /**
     * 文本提示
     * @param  string $title    
     * @param  string $text     
     * @param  array  $assignee 
     * @return 
     */
    private function text($title,$text,$assignee=[],$isall=false){

        return 
        [
            'msgtype'=>'text',
            'text'=>['content'=>$text],
            'at'=>['atMobiles'=>$assignee,'isAtAll'=>$isall]
        ];
    }

    private function single($title,$text,$url_title,$url,$assignee=[]){

        return 
        [
            'msgtype'=>'actionCard',
            'actionCard'=>[
                'title'=>$title,
                'text'=>$text,
                'singleTitle'=>$url_title,
                'singleURL'=>$url,
                'btnOrientation'=>'1',
                'hideAvatar'=>'0'
            ]
        ];
    }

    /**
     * 以markdown格式传输
     * @param  string $title    
     * @param  string $text     
     * @param  array $assignee 
     * @return 
     */
    private function markdown($title,$text,$assignee=[],$isall=false){

        return 
        [
            'msgtype'=>'markdown',
            'markdown'=>['title'=>$title,'text'=>$text],
            'at'=>['atMobiles'=>$assignee,'isAtAll'=>$isall]
        ];
    }

    /**
     * 卡片的形式
     * @param  string $title  
     * @param  string $text   
     * @param  string $branch 
     * @return []
     */
    private function card($title,$text,$btns){

        return 
        [
            'msgtype'=>'actionCard',
            'actionCard'=>[
                'title'=>$title,
                'text'=>$text,
                'btns'=>$btns,
                'btnOrientation'=>'1',
                'hideAvatar'=>'0'
            ]
        ];
    }
    /**
     * 文本链接的方式
     * @param  string $title 
     * @param  string $text  
     * @param  string $url   
     * @return 
     */
    private function link($title,$text,$url,$image){

        return
        [
            'msgtype'=>'link',
            'link'=>[
                'title'=>$title,
                'text'=>$text,
                'messageUrl'=>$url,
                'picUrl'=>$image
            ]
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
        @file_put_contents('/tmp/dingtalk.log', var_export($data,true).PHP_EOL,FILE_APPEND);        
        curl_setopt($ch, CURLOPT_URL, $this->notify_url."?access_token=".$this->access_token);

        if( ! $result = curl_exec($ch)) {
            $error=curl_error($ch);
        }
        print_r($result);
        curl_close($ch);
        @file_put_contents('/tmp/dingtalk.log', var_export($result,true).PHP_EOL,FILE_APPEND);
        return $result;
    }
}