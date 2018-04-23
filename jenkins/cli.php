<?php

class JenkinsCli {

    const JENKINS_URL='http://cd.vrcdkj.cn';

    const USER_NAME='admin';

    const USER_PWD='admin';

    private $commands=['buildWithParameters'];

    /**
     * deploy branch on env 
     * @param  string $env 环境   
     * @param  string $branch 
     * @return 
     */
    public function deploy($env,$branch){

        $jobs=$this->getJobs();

        $job=$jobs[$env];

        if(empty($job)) return false;

        $action="/job/{$job}/buildWithParameters";

        $params=['branch_and_tags'=>'origin/'.$branch];
        // print_r($params);exit;
        return $this->http($action,$params);
    }


    private function getJobs(){

        return 
        [
            // 'test'=>'test_vrcdkj',
            'dev'=>'dev_vrcdkj',
            'staging'=>'staging_vrcdkj',
            'webhook'=>'webhook_vrcdkj',
            'doc'=>'doc_vrcdkj',
            'fdev'=>'fdev_vrcdkj',
            'fadmin'=>'fadmin_vrcdkj',
            'fexpert'=>'fexpert_vrcdkj',
            'staging_expert'=>'staging_expert_vrcdkj',
            'staging_admin'=>'staging_fsb_vrcdkj',
            'staging_api'=>'fstaging_vrcdkj',
        ];
    }

    private function http($action,$params){

        $ch=curl_init();

        curl_setopt($ch,CURLOPT_TIMEOUT,60);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_USERPWD, self::USER_NAME.":".self::USER_PWD);
        
        curl_setopt($ch, CURLOPT_URL, self::JENKINS_URL.$action);

        if( ! $result = curl_exec($ch)) {
            $error=curl_error($ch);
        }
        print_r($result);
        curl_close($ch);
        @file_put_contents('/tmp/jenkins.log', var_export($result,true).PHP_EOL,FILE_APPEND);
        return $result;
    }
}