<?php
namespace Framework;
class Upload
{
	//路径
	protected $path = './Upload';
	//准许的mime类型
	protected $allowMime = array('image/jpeg' , 'image/png' , 'image/gif' , 'image/wbmp');
	//准许的文件后缀名
	protected $allowSub = array('jpg' , 'png' , 'gif' , 'wbmp' , 'jpeg');
	//准许的大小
	protected $allowSize = 2000000;
	//文件的错误号
	protected $errorNum;
	//文件的错误信息
	protected $errorInfo;
	//文件大小
	protected $size;
	//文件的新名字
	protected $newName;
	//文件的原名字
	protected $orgName;
	//随机文件名
	protected $isRandName = true;
	//临时文件名
	protected $tmpName;
	//文件的前缀
	protected $preFix;
	//文件的后缀
	protected $subFix;
	//上传文件的mime类型
	protected $type;
	
	//初始化成员属性
	public function __construct($array = array())
	{
		
		//var_dump($array);
		//var_dump();
		foreach ($array as $key => $val) {
			$keys = strtolower($key);
			
			if (!array_key_exists($keys , get_class_vars(get_class($this)))) {
				continue;
			}
			
			//通过一个方法实现批量赋值
			$this->setOption($keys , $val);
			
		}
	}
	
	//上传文件的方法
	public function up($fields)  //input表单里面的name值
	{
		
		//var_dump($_FILES);
		//var_dump($fields);
		
		//检测路径是否存在
		if (!$this->checkPath()) {
			exit('没有上传文件');
		}
		
		//获取文件的各种信息
		$name = $_FILES[$fields]['name'];
		$type = $_FILES[$fields]['type'];
		$tmpName = $_FILES[$fields]['tmp_name'];
		$error = $_FILES[$fields]['error'];
		$size = $_FILES[$fields]['size'];
		
		//setFiles
		if ($this->setFiles($name , $type , $tmpName , $error , $size)) {
			//是否启用随机文件的名字
			$this->newName = $this->createName();
			
			//echo $this->newName;
			
			if ($this->checkMime() && $this->checkSub() && $this->checkSize()) {
				
				//移动文件
				if ($this->move()) {
					return $this->path;
				} else {
					return false;
				}
			} else {
				return false;
			}	
			
		}
	}
	//移动文件
	protected function move()
	{
		//
		if (is_uploaded_file($this->tmpName)) {
			$this->path = rtrim($this->path , '/') . '/' . $this->newName;
			//移动文件
			if (move_uploaded_file($this->tmpName , $this->path)) {
				return true;
			} else {
				$this->setOption('errorNum' , -6);
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	//检测大小
	protected function checkSize()
	{
		if ($this->size > $this->allowSize) {
			$this->setOption('errorNum' , -5);
			var_dump($this->errorNum);
			return false;
		} else {
			return true;
		}
	}
	//检测后缀
	
	protected function checkSub()
	{
		if (in_array($this->subFix , $this->allowSub)) {
			return true;
		} else {
			$this->setOption('errorNum' , -4);
			return false;
		}
	}
	//检测mime类型
	protected function checkMime()
	{
		if (in_array($this->type , $this->allowMime)) {
			return true;
		} else {
			$this->setOption('errorNum' , -3);
			return false;
		}
	}
	
	//随机文件名
	protected function createName()
	{
		if ($this->isRandName) {
			return $this->preFix . $this->randName();
		} else {
			return $this->preFix . $this->orgName;
		}
	}
	//随机文件名
	protected function randName()
	{
		return uniqid() . '.' . $this->subFix;
	}
	//setFiles
	protected function setFiles($name , $type , $tmpName , $error , $size)
	{
		if ($error) {
			// 1 2 3 4 6 7

			$this->setOption('errorNum' , $error);
		}
		$this->orgName = $name;
		$this->type = $type;
		$this->tmpName = $tmpName;
		$this->size = $size;
		
		//获取文件的后缀
		//var_dump($name);
		$arr = explode('.' , $name);
		
		$this->subFix = array_pop($arr);
		
		return true;
		
	}
	
	
	//检测路径
	protected function checkPath()
	{
		if (empty($this->path)) {
			$this->setOption('errorNum' , -1);
			return false;
		} else {
			
			if (file_exists($this->path) && is_writeable($this->path)) {
				return true;
			} else {
				$this->path = rtrim($this->path , '/') . '/';
				
				if (mkdir($this->path , 0777 , true)) {
					return true;
				} else {
					$this->setOption('errorNum' , -2);
					return false;
				}
				
			}
			
		}
	}
	//获取错误号
	protected function getErrorNum()
	{
		
		$str = '';
		switch ($this->errorNum) {
			case -1:
				$str = '没有上传文件';
				break;
			case -2:
				$str = '文件夹创建失败';
				break;
			case -3:
				$str = '不准许的mime类型';
				break;
			case -4:
				$str = '不准许的文件的后缀';
				break;
			case -5:
				$str = '不准许的文件的大小';
				break;
			case -6:
				$str = '上传失败';
				break;
			case 1:
				$str = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。 ';
				break;
			case 2:
				$str = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
				break;
			case 3:
				$str = '文件只有部分被上传';
				break;
			case 4:
				$str = '没有文件被上传';
				break;
			case 6:
				$str = '找不到临时文件夹';
				break;
			case 7:
				$str = '文件写入失败';
				break;
			
		}
		return $str;
	}
	
	//设置成员属性 还有 错误号
	protected function setOption($keys , $val)
	{
		
		$this->$keys = $val;
	}
	
	//
	/*
	public function __destruct()
	{
		echo $this->getErrorNum();
	}
	*/
	
	public function __get($name)
	{
		if ($name == 'errorInfo') {
			return $this->getErrorNum();
		}
	}
}





