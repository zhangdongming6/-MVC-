<?php
namespace Framework;
class Verify
{
	//宽
	protected $width;
	//高
	protected $height;
	//图片的类型
	protected $imgType;
	//字体的类型
	protected $type;
	//字体的个数
	protected $num;
	//资源
	protected $img;
	//画布上的字符串
	protected $getCode;
	
	
	//初始化
	
	public function __construct($width = 100, $height = 40, $imgType='png', $num=4 , $type = 3)
	{
		$this->width = $width;
		$this->height = $height;
		$this->imgType = $imgType;
		$this->num = $num;
		$this->type = $type;
		
		$this->getCode = $this->getCode();
		
	}
	
	
	//获取字符串
	public function getCode()
	{
		$string = '';
		
		switch ($this->type) {
			case 1:
				$string = join('' , array_rand(range(0,9) , $this->num));
				break;
			case 2:
				$string = implode('' , array_rand(array_flip(range('a' , 'z')) , $this->num));
				break;
				
			case 3:
				//$str = 'abcdefghijklmnnopqrestuvwxyzABCDEFALJRERWRSEFJKSDLFK0123456789';
				//$string = substr(str_shuffle($str) , 0 , $this->num);
				for ($i=0;$i<$this->num;$i++) {
					$rand = mt_rand(0,2);
					
					switch ($rand) {
						case 0:
							$char = mt_rand(48 , 57);
							break;
						case 1:
							$char = mt_rand(65 , 90);
							break;
						case 2:
							$char = mt_rand(97 , 122);
							break;
					}
					$string .= sprintf('%c' , $char);
				}
				break;
		}
		
		return $string;
	}
	
	//创建画布
	protected function createImg()
	{
		$this->img = imagecreatetruecolor($this->width , $this->height);
	}
	//背景颜色
	protected function bgColor()
	{
		return imagecolorallocate($this->img , mt_rand(130 , 255) , mt_rand(130 , 255) , mt_rand(130 , 255));
	}
	
	//字体颜色
	protected function fontColor()
	{
		return imagecolorallocate($this->img , mt_rand(0 , 120) , mt_rand(0 , 120) , mt_rand(0 , 120));
	}
	
	//填充背景色
	protected  function fill()
	{
		return imagefilledrectangle($this->img , 0 , 0 , $this->width , $this->height , $this->bgColor());
	}
	
	//画像素  ？？？
	protected function pixed()
	{
		for ($i=0;$i<100;$i++) {
			imagesetpixel($this->img , mt_rand(0 , $this->width) , mt_rand(0 , $this->height) , $this->fontColor());
		}
	}
	
	//画线
	protected function arc()
	{
		for ($i=0;$i<3;$i++){
			imagearc($this->img , mt_rand(10 , $this->width) , mt_rand(10 , $this->height) , mt_rand(10 , $this->width) , mt_rand(10 , $this->height) , 20 , 180 , $this->fontColor());
		}
	}
	
	//写字
	protected function write()
	{
		for ($i=0;$i<$this->num;$i++) {
			$x = ceil($this->width/$this->num) * $i;
			$y = mt_rand(10 , $this->height - 10);
			imagechar($this->img , 5 , $x , $y , $this->getCode[$i] , $this->fontColor());
		}
	}
	
	//输出图片
	protected function out()
	{
		$func = 'image'.$this->imgType;
		
		$header = 'Content-type:image/' . $this->imgType;
		
		if (function_exists($func)) {
			$func($this->img);
			header($header);
		} else {
			
			exit('不支持的图片的格式');
		}
	}
	
	//得到图片
	public function getImg()
	{
		$this->createImg();
		$this->fill();
		$this->arc();
		$this->pixed();
		$this->write();
		$this->out();
		return $this->getCode;
	}
	
	//销毁资源
	
	public function __destruct()
	{
		imagedestroy($this->img);
	}
}







