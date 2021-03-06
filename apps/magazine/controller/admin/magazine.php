<?php
/**
 * 杂志管理
 *
 * @aca 杂志管理
 */
final class controller_admin_magazine extends magazine_controller_abstract
{
	private $magazine, $edition, $page, $content, $pagesize = 10;

	function __construct($app)
	{
		parent::__construct($app);
		$this->magazine   = loader::model('admin/magazine');
		$this->edition   = loader::model('admin/edition');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'中英文刊物'));
		$this->view->display("magazine/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$data = $this->magazine->getMagazines();
		echo $this->json->encode($data);
	}

    /**
     * 保存
     *
     * @aca 保存
     */
	function save()
	{
		if ($this->is_post())
		{
			$_POST['logo'] = $this->json->encode($_POST['logo']);
			if ($id = $this->magazine->save($_POST))
			{
				$result = array('state'=>true, 'data'=>$this->magazine->getMagazine($id));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->magazine->error());
			}
			exit($this->json->encode($result));
		}
		else
		{
			$magazine = $this->magazine->get(intval($_GET['id']));
			if(!$magazine['template_content']) 
			{
				$magazine['template_list'] = 'magazine/list.html';
				$magazine['template_content'] = 'magazine/content.html';
			}
			$this->view->assign('magazine', $magazine);
		    $this->view->display('magazine/form');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     * @return mixed
     */
	function delete()
	{
		$id = $_GET['id'];
		if(!$id) return ;
		$this->magazine->delete($id);
		$result = array('state'=>true);
		exit($this->json->encode($result));
	}

	 /**
     * 上传
     *
     * @aca 上传
     * @return 路径
     */
	public function upload()
    {
        $attachment = loader::model('admin/attachment', 'system');
        if (!$file = $attachment->upload('Filedata', true, null, 'jpg|jpeg|png|gif', 1024, array())) {
            exit($this->json->encode(array('state' => false)));
        }
        $result['aid'] = $attachment->aid[0];
        $result['file'] = $file;

        exit($this->json->encode(array('state' => true, 'data' => $result)));
    }

}