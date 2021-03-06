<?php

class JiraMessage {

    private $host='http://101.201.234.44:8080';

    private $project;

    private $url;

    private $title;

    private $summary;

    private $assignee;

    private $creator;

    private $priority;

    private $issue_type;

    private $issue;

    /**
     * parent issue key
     * @var 
     */
    private $parent_issue;

    private $is_transition=false;

    public $is_resolved=false;

    public $is_reopened=false;

    private $is_comment=false;

    /**
     * 是否需要在staging上测试
     * @var boolean
     */
    public $test_staging=false;
    public $test_dev=false;

    private $comment;
    /**
     * 操作人
     * @var string
     */
    private $operator;

    private $changelogs=[];

    /**
     * 关联的附近
     * @var array
     */
    private $attachfiles=[];

    public function __construct($project,$post){
        // 项目名
        $this->project=$project;
        // 标题
        $this->title=$post['issue']['fields']['summary'];
        // 摘要
        $this->summary=$post['issue']['fields']['description'];
        // assign display name
        $this->assignee=$post['issue']['fields']['assignee']['displayName'];
        // assignee name
        $this->assignee_name=$post['issue']['fields']['assignee']['name'];
        // 创建者
        $this->creator=$post['issue']['fields']['creator']['displayName'];
        // 优先级
        $this->priority=$post['issue']['fields']['priority'];
        // issue号
        $this->issue=$post['issue']['key'];
        // issue parent key
        $this->parent_issue=$post['issue']['parent']['key'];
        // issue 类型
        $this->issue_type=$post['issue']['fields']['issuetype']['name'];
        // // 进度通知
        // $this->is_transition=isset($post['transition']);

        // if($this->is_transition){
        //     // issue的状态是否已完成
        //     $this->is_resolved=$post['transition']['transitionName']=='开发完成';
        //     // issue的状态是否已reopen
        //     $this->is_reopened=$post['transition']['transitionName']=='重新开发';
        // }
        // 更新是否是新增备注
        $this->is_comment=$post['comment'];

        if($this->is_comment){

            $this->comment=['content'=>$post['comment']['body'],'author'=>$post['comment']['author']['displayName']];
        }

        $this->operator=$this->getDisplayName($post['user']['displayName']);

        $this->changelogs=[];

        if($post['changelog']){

            foreach ($post['changelog']['items'] as $change) {

                if($change['field']=='assignee'){

                    $this->changelogs[]='开发者由'.$change['fromString'].'改成'.$change['toString'];
                    
                    if($post['comment']['body']){

                        $this->changelogs[]=$this->getDisplayName($post['comment']['author']['displayName'])."添加评论: ".$post['comment']['body'];
                    }
                    
                }else if($change['field']=='description'){

                    // $this->changelogs[]='issue内容由'.$change['fromString']."\n\n>改成\n\n>".$change['toString']; 
                }else if($change['field']=='status'){
                    // 10101:等待上线 10100:staging测试
                    if($change['from']==10101 && $change['to']==10100){

                        $this->test_staging=true;
                    }elseif($change['from']==10004 && $change['to']==10001){

                        $this->is_resolved=true;
                    }elseif($change['to']==10007){

                        $this->is_reopened=true;
                    }elseif($change['from']==10001 && $change['to']==10005){
                        $this->test_dev=true;
                    }elseif($change['from']==10004 && $change['to']==10104){
                        $this->is_resolved=true;
                    }
                }elseif($change['field']=='Attachment'){

                    foreach ($post['issue']['attachment'] as $attachment) {
                        
                        if($attachment['id']==$change['to']){

                            if(in_array($attachment['mimeType'], ['image/png','image/jpeg','image/jpg'])){

                                $this->attachfiles['image'][]=$attachment['content'];  
                            }
                        }
                    }
                }elseif($change['field']=='priority'){
                    $from=$this->getPriorityName($change['fromString']);
                    $to=$this->getPriorityName($change['toString']);
                    $this->changelogs[]='优先级状态由'.$from.'改成'.$to;
                }
            }
        }
    }

    /**
     * get jira all register users,key is the name,value is the dingtalk register phone.
     * @return array
     */
    private function getUsers(){

        return
        [
            'admin'=>'15763951212',
            '魏山'=>'15763951212',
            'dawn'=>'18610402391',
            '郑生齐'=>'18610402391',
            'lishuo'=>'13911516907',
            'nexiy'=>'18811223158',
            'wuchao'=>'13522216112',
            '王娟娟'=>'13161824260',
            '肖阿勇'=>'18612309283',
            '韩萌萌'=>'18330236860',
            'lijialin'=>'15910442846',
            'haohuili'=>'18501219135',
            'baibaoqiang'=>'15201496976',
            'liuyanmei'=>'13401162965',
            'kongbin'=>'15801261092',
            'webxu'=>'15011103838',
            // andriod
            'jingke'=>'17701304842',
            'wangliang'=>'15810359716',
            '石头'=>'13521092668',
            // ios
            'jiamao'=>'15010981834',
            'fulong0320'=>'17611228816',
            'liubaoheng'=>'18337142091',
            '张茹'=>'18511306676',
        ];
    }

    private function getDisplayName($name){

        switch ($name) {
            case 'admin':return '魏山';break;
            case 'lijialin':return '李嘉临';break;
            default: return $name;
        }
    }

    /**
     * 获取项目列表
     * @return array
     */
    public function getProjects(){

        return 
        [
            'php',
            'android',
            'ios',
            'go'
        ];
    }

    public function getPriorityName($eng){

        switch (strtolower($eng)) {
            case 'major':return '一般';break;
            case 'blocker':return '紧急';break;
            case 'critical':return '重要';break;
            case 'minor':return '次要';break;
            case 'trivial':return '无关紧要';break;
        }
    }

    /**
     * 获取组成员
     * @return array
     */
    public function getGroups(){

        return 
        [
            'php'=>[
                'test'=>['18330236860','15910442846','18501219135'],
                'dev'=>['15763951212','18610402391','18811223158','13522216112','13161824260','18612309283']
            ]
        ];
    }

    public function getProject(){

        return $this->project;
    }

    public function getIssueTitle(){

        return $this->title;
    }

    public function getIssueSummary(){

        return $this->summary;
    }

    /**
     * 返回指定assignee
     * @return string 
     */
    public function getIssueAssignee(){

        return $this->assignee;
    }

    /**
     * 返回指定assign的手机号(用于钉钉)
     * @return string 
     */
    public function getIssueAssigneePhone(){

        $users=$this->getUsers();

        return $users[strtolower($this->assignee_name)];
    }

    /**
     * get issue priority name and icon
     * @return array
     */
    public function getIssuePriority(){

        return ['icon'=>$this->priority['iconUrl'],'name'=>$this->priority['name']];
    }

    /**
     * get issue link
     * @return string
     */
    public function getIssueUrl(){

        return sprintf("%s/browse/%s",$this->host,$this->issue);
    }

    public function getIssueType(){

        return $this->issue_type;
    }

    public function getIssueNumber(){

        return $this->issue;
    }

    public function getIssueParentNumber(){

        return $this->parent_issue;
    }

    public function getIssueCreator(){

        return $this->getDisplayName($this->creator);
    }

    public function getChangeLogs(){

        return $this->changelogs;
    }

    /**
     * 获取issue新增备注
     * @return array
     */
    public function getIssueNewComment(){

        return $this->comment;
    }

    /**
     * 获取更改issue实际操作人
     * @return array
     */
    public function getIssueOperator(){

        return $this->operator;
    }

    public function getAttachFiles(){

        return $this->attachfiles;
    }

    /**
     * get jira host address
     * @return string
     */
    public function getJiraHost(){

        return $this->host;
    }

    public function isTransition(){

        return $this->is_transition;
    }

    public function isComment(){

        return $this->is_comment;
    }

    /**
     * 获取评论
     * @return array
     */
    public function getComment(){

        return $this->comment;
    }

    /**
     * Check issue has resolved
     * @return boolean
     */
    public function IsIssueResolved(){

        return $this->is_resolved;
    }

    /**
     * Check issue has reopened
     * @return  boolean
     */
    public function IsIssueReopened(){

        return $this->is_reopened;
    }
}