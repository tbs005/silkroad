<?php
/*
  +付费站推送免费站
  +类型：视频
  +author:kanzhi
*/
class controller_admin_push_video extends system_controller_abstract
{
	public function __construct($app)
	{
		parent::__construct($app);
		$this->gateway="http://api.silkroad.news.cn/";
		$this->auth_key="f766b428cace0dddbd52050e3cc56a6f";
		$this->auth_secret="206cf2d4a060dc1c80278498da051d74";
		$this->api_url="?app=video&controller=video&action=add";
		$this->catid_list=config('push'); /*读取推送栏目对应关系*/
		$this->db=factory::db();
		$this->pagesize=100;
		$this->start_time=strtotime("00:00:00");	//每天的开始时间
		$this->end_time=strtotime("23:59:59");		//每天的结束时间
	}

	/*计划任务推送视频*/
	public function cron_video()
	{
		$old_catid=array_keys($this->catid_list);

		/*遍历付费站栏目读取相关联的文章数据*/
		foreach($old_catid as $value)
		{
			$this->get_catid_video($value);
		}

		exit('{"state":true}');

	}

	private function get_catid_video($catid)
	{
		//
		$field="c.contentid,catid,title,color,tags,subtitle,video,sourceid,v.editor,playtime,description,thumb,published,allowcomment";
		$where=" where catid=$catid and status=6 and weight>=80 and modelid=4 and published > $this->start_time and published <=$this->end_time order by published desc";
		//$where=" where catid=$catid and status=6 and weight>=80 and modelid=4 order by published desc";
		/*统计符合查询的条件数量*/
		$total=$this->db->select("select count(contentid) as contentid from #table_content as c $where");



		$count=ceil($total[0]['contentid']/$this->pagesize);

		/*如果该栏目下的数量小于1，则返回false*/
		if($count<1)
		{
			return false;
		}


		for($i=1;$i<=$count;$i++)
		{   
			$page=$i-1;
			$start=$page*$this->pagesize;
			$limit=" limit $start".",".$this->pagesize;
			$arr=$this->db->select("select $field from #table_content as c inner join #table_video as v on c.contentid=v.contentid $where $limit");
			$this->insert_video($arr);
		}	
		//
	}


	/*遍历数组，将每一条文章进行推送*/
	public function insert_video($arr)
	{

			foreach($arr as $val)
			{
					$val['catid']=$this->catid_list[$val['catid']];	
					$val['weight']=20;
					$val['saveremoteimage']=1;	//下载远程图片本地化
					$val['publishedby']=1;
					$val['published']=date("Y-m-d H:i",$val['published']);
					$val['status']=6;
					$sourceid = $val['sourceid'];
					$source=$this->db->get("select name from cmstop_source where sourceid=$sourceid");
					$val['source']=$source['name'];
					unset($val['sourceid']);
					if(!empty($val['thumb']))
					{
						$val['thumb']=UPLOAD_URL.$val['thumb'];
					}
					$result=$this->push_video($val);	
					
			}

	}

	/*将数据通过添加文章接口，推送到免费站*/
	public function push_video($post)
	{
		ksort($post);



		$sign=md5(http_build_query($post).$this->auth_secret);
		$request_url = $this->gateway.$this->api_url.'&key='.$this->auth_key.'&sign='.$sign;
		
		$answer=request($request_url,$post);

		//var_dump($answer);

	}




}