<?php

class JiraMessage {

    private $host='http://101.201.234.44:8080';

    private $url;

    private $title;

    private $summary;

    private $assignee;

    private $priority;

    private $issue;

    private $is_transition=false;

    private $is_resolved=false;

    private $is_reopened=false;

    public function __construct($post){
        // 标题
        $this->title=$post['issue']['fields']['summary'];
        // 摘要
        $this->summary=$post['issue']['fields']['description'];
        // assign
        $this->assignee=$post['issue']['fields']['assignee']['displayName'];
        // 优先级
        $this->priority=$post['issue']['fields']['priority'];
        // issue号
        $this->issue=$post['issue']['key'];

        $this->is_transition=isset($post['transition']);

        if($this->is_transition){

            $this->is_resolved=$post['transition']['transitionName']=='开发完成';

            $this->is_reopened=$post['transition']['transitionName']=='重新开发';
        }
    }

    /**
     * get jira all register users,key is the name,value is the dingtalk register phone.
     * @return array
     */
    private function getUsers(){

        return
        [
            'admin'=>'15763951212'
        ];
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

        return $users[strtolower($this->assignee)];
    }

    /**
     * get issue priority name and icon
     * @return array
     */
    public function getIssuePriority(){

        return ['icon'=>$this->priority,'name'=>$this->priority['name']];
    }

    /**
     * get issue link
     * @return string
     */
    public function getIssueUrl(){

        return sprintf("%s/%s",$this->host,$this->issue);
    }

    /**
     * get jira host address
     * @return string
     */
    public function getJiraHost(){

        return $this->host;
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

        return $this->is_reopen;
    }
}