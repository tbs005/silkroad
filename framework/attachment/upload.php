<?php
import('attachment.abstract');

class upload extends attachment_abstract 
{
	public $allow_exts = 'gif|jpg|jpeg|bmp|png|txt|zip|rar|doc|docx|xls|ppt|pdf|swf|flv|mp4', //如果是*，代表任意类型
	       $maxfilesize = 1024;
	
	function __construct($dir = null, $allow_exts = null, $maxfilesize = null)
	{
		parent::__construct($dir);
		$this->set($dir, $allow_exts, $maxfilesize);
	}

    function set($dir, $allow_exts = null, $maxfilesize = null)
    {
    	if (!is_null($maxfilesize)) {
            $this->maxfilesize = $maxfilesize;
        }
        $this->maxfilesize = $this->maxfilesize * 1024;
        parent::set($dir, $allow_exts);
    }
    
    function execute($field, $rename = false)
    {
    	if (!isset($_FILES[$field]) || !$_FILES[$field]['name'])
    	{
    		$this->errno = 4;
    		return false;
    	}
    	
        $info = array();
    	if (is_array($_FILES[$field]['name']))
    	{
    		foreach ($_FILES[$field]['name'] as $key=>$name)
    		{
    			$file = array('tmp_name'=>$_FILES[$field]['tmp_name'][$key],
    			              'name'=>$_FILES[$field]['name'][$key],
    			              'size'=>$_FILES[$field]['size'][$key],
    			              'type'=>$_FILES[$field]['type'][$key],
    			              'error'=>$_FILES[$field]['error'][$key],
    			              'rename'=>$rename
    			              );
    			$result = $this->move($file);
    			if (!$result) return false;
    			$info[] = $result;
    		}
    	}
    	else 
    	{
    		$file = $_FILES[$field];
    		$file['rename'] = $rename;
    		$info = $this->move($file);
    	}
    	return $info;
    }
    
    function error()
    {
    	$ERROR = array(1=>'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
    	               2=>'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
    	               3=>'文件只有部分被上传',
    	               4=>'没有文件被上传',
    	               6=>'找不到临时文件夹',
    	               7=>'文件写入失败',
    	               10=>'上传文件格式不符合要求',
    	               11=>'重命名文件格式不符合要求',
    	               12=>'上传文件太大',
    	               13=>'文件移动出错',
    	               );
    	return $ERROR[$this->errno];
    }
    
    private function check($file)
    {
		if ($file['error'] !== UPLOAD_ERR_OK)
		{
			$this->errno = $file['error'];
			return false;
		}

		if ($file['size'] > $this->maxfilesize)
		{
			$this->errno = 12;
			return false;
		}
		
		if (!is_uploaded_file($file['tmp_name']))
		{
			$this->errno = 13;
			return false;
		}

		if ($this->allow_exts != '*' && !preg_match("/\.($this->allow_exts)$/i", $file['name']))
		{
			$this->errno = 10;
			return false;
		}
		return true;
    }
    
    private function move($file)
    {
    	if (!$this->check($file)) return false;
		
		$fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if ($file['rename'] === true)
		{
			$this->set_filename(null, $fileext);
		}
		elseif ($file['rename'] === false)
		{
			$this->set_filename($file['name']);
		}
		else 
		{
			$this->set_filename($file['rename']);
		}
		
        $this->target = $this->dir.$this->filename;
		if (!@move_uploaded_file($file['tmp_name'], $this->target))
		{
			$this->errno = 13;
			return false;
		}
		@unlink($file['tmp_name']);

		$filepath = $this->format(dirname($this->target), false).'/';
		$info = array('alias'=>$file['name'], 'filename'=>$this->filename, 'filepath'=>$filepath, 'filemime'=>$file['type'], 'fileext'=>$fileext, 'filesize'=>$file['size']);
		if ($this->is_image($file['name']))
		{
			$info['isimage'] = 1;
		}
	    $this->files[] = $info;
		return $info['filepath'].$info['filename'];
    }
}