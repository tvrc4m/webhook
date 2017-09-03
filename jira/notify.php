<?php

class JiraNotify {

    /**
     * issue标题
     * @var string
     */
    protected $title;

    /**
     * issue号
     * @var string
     */
    protected $key;

    /**
     * 开发者
     * @var string
     */
    protected $assignee;

    public function __construct($title,$key,$assignee){

        $this->title=$title;
        $this->key=$key;
        $this->assignee=$assignee;
    }

    public function issueUpdated($changelog){


    }


    public function issueCreate(){


    }
}