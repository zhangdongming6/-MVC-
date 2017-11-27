<?php
namespace Framework;
class Page
{
	
	//������
	protected $total;
	//��ҳ��
	protected $pageCount;
	//ÿҳ����ʾ��
	protected $num;
	//ƫ����
	protected $offset;
	//url������
	protected $url;
	//��ǰҳ
	protected $page;
	
	
	public function __construct($total , $num = 5)
	{
		//����������
		$this->total = $this->getTotal($total);
		
		//echo $this->total;
		//ÿҳ��ʾ��
		$this->num = $num;
		//������������ÿҳ��ʾ�������ҳ��
		$this->pageCount = $this->getPageCount();
		//�����ǰҳ

		$this->page = $this->getPage();
		
		//echo $this->page;
		
		//echo $this->pageCount;
		//�����ƫ����
		$this->offset = $this->getOffset();

		//echo $this->offset;  �����һ����˿��
		
		//��ȡURl  
		$this->url = $this->getUrl();
		//echo $this->url;
		
		
		
		
	}
	
	
	//����url ��ҳ ��һҳ ��һҳ βҳ
	
	
	protected function setUrl($page)
	{
		if (strstr($this->url , '?')) {
			return $this->url. '&page=' . $page;
		} else {
			return $this->url. '?page=' . $page;
		}
	}
	//������ҳ������
	protected function first()
	{
		return $this->setUrl(1);
	}
	
	//������һҳ
	protected function prev()
	{
		$page = (($this->page - 1) < 1) ? 1 : ($this->page - 1);
		
		return $this->setUrl($page);
	}
	
	//��һҳ
	protected function next()
	{
		$page = (($this->page + 1) > $this->pageCount ) ? $this->pageCount : ($this->page + 1);
		return $this->setUrl($page);
	}
	
	//βҳ
	protected function last()
	{
		return $this->setUrl($this->pageCount);
	}
	
	//����url
	protected function getUrl()
	{
		//var_dump($_SERVER);
		
		//��ȡ�ļ���ַ
		$path = $_SERVER['SCRIPT_NAME'];
		
		//echo $path;
		//��ȡ������
		$host = $_SERVER['SERVER_NAME'];
		//��ȡ�˿ں�
		$port = $_SERVER['SERVER_PORT'];
		//��ȡЭ��
		$scheme = $_SERVER['REQUEST_SCHEME'];
		//��ȡ����
		$queryString = $_SERVER['QUERY_STRING'];
		
		if (strlen($queryString)) {
			parse_str($queryString , $array);
			//var_dump($array);
			unset($array['page']);
			//var_dump($array);
			
			$path = $path . '?' . http_build_query($array);
			//echo $path;/1710/4/Page.php?username=niuxi&password=123
		}
		$url = $scheme . '://' . $host . ':' . $port . $path;
		return  $url;
	}
	
	//����ǰҳ
	
	public function getPage()
	{
		return isset($_GET['page']) ? $_GET['page'] : 1;
	}
	
	//����ƫ����
	
	public function getOffset()
	{
		$start = ($this->page - 1) * $this->num;  //
		
		$limit = 'limit ' . $start . ',' . $this->num;
		
		return $limit;
	}
	
	
	//������ҳ��
	public function getPageCount()
	{
		return ceil($this->total / $this->num);
	}
	
	
	//����������
	protected function getTotal($total)
	{
		return ($total < 1) ? 1 : $total;
	}
	
	
	//��¶������ʹ�õ�
	public function render()
	{

		return [
			'first' => $this->first(),
			'prev' => $this->prev(),
			'next' => $this->next(),
			'last' => $this->last()
		];
	}
	
	
	
}