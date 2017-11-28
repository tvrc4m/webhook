<?php

class GitlabApi {

    private $host='http://gitlab.kanfanews.com/api/v3';
    /**
     * 私人token
     * @var string
     */
    private $private_token='dBfZV2M_zYhF1gDcKKWX';

    /**
     * 项目id
     * @var integer
     */
    private $project_id=3; // php/news.git
    // private $project_id=11; // shan/test.git
    /**
     * 错误消息
     * @var string
     */
    public $error_msg;

    /**
     * http状态码
     * @var int
     */
    public $http_code;

    /**
     * 列举gitlab的项目
     * @return array
     */
    public function listProjects(){

        $url="/projects";

        $result=$this->http($url,'GET');

        return $result;
    }

    /**
     * 发起合并请求
     * @param  string $src_branch  源分支
     * @param  string $dest_branch 目标分支，将源分支合并到目标分支
     * @param  string $title       merge标题
     * @return 
     */
    public function createMergeRequest($src_branch,$dest_branch,$title){

        $url='/projects/'.$this->project_id.'/merge_requests';

        $result=$this->http($url,'POST',['id'=>uniqid(),'source_branch'=>$src_branch,'target_branch'=>$dest_branch,'title'=>$title]);

        return $result;
    }

    /**
     * 接受合并请求
     * @param  int $merge_request_iid 合并请求的id
     * @return boolean
     */
    public function acceptMergeRequest($merge_request_iid){

        $url="/projects/".$this->project_id.'/merge_requests/'.$merge_request_iid.'/merge';

        $result=$this->http($url,'PUT',[]);

        return $result;
    }

    public function delMergeRequest($merge_request_iid){

        $url="/projects/".$this->project_id.'/merge_requests/'.$merge_request_iid;

        $result=$this->http($url,'DELETE',[]);

        return $result;
    }

    /**
     * 获取分支名
     * @param  string $branch 
     * @return array
     */
    public function getBranch($branch){

        $url='/projects/'.$this->project_id.'/repository/branches/'.$branch;

        $result=$this->http($url,'GET');

        return $result;
    }

    /**
     * 删除分支
     * @param  string $branch 
     * @return 
     */
    public function deleteBranch($branch){

        $url="/projects/".$this->project_id."/repository/branches/".$branch;

        return $this->http($url,'DELETE');
    }

    /**
     * 删除已经合并的分支
     * @return 
     */
    public function deleteMergedBranch(){

        $url="/projects/".$this->project_id."/repository/merged_branches";

        return $this->http($url,'DELETE');
    }

    public function revertMergeRequest(){


    }

    private function http($url,$method='GET',$data=[]){

        $ch=curl_init();

        curl_setopt($ch,CURLOPT_TIMEOUT,60);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json;charset=utf-8','PRIVATE-TOKEN: '.$this->private_token]);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if(strtoupper($method)=='GET'){
            $params=http_build_query($data);
            $url.='?'.$params;
        }elseif(strtoupper($method)=='PUT'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }elseif(strtoupper($method)=='DELETE'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }else{
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        curl_setopt($ch, CURLOPT_URL, $this->host.$url);

        if( ! $result = curl_exec($ch)) {
            $error=curl_error($ch);
        }

        $this->http_code=curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);

        $response=json_decode($result,true);

        @file_put_contents('/tmp/gitlab.log', var_export($response,true).PHP_EOL,FILE_APPEND);

        return $response;
    }
}