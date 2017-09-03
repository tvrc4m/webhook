<?php

class JiraMessage {

    private $host='http://101.201.234.44:8080';

    private $url;

    private $title;

    private $summary;

    private $assignee;

    private $priority;

    private $issue;

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
    }

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
}