<?php
namespace Framework;
class Model
{
	//链接
	protected $link;
	//主机名
	protected $host;
	//用户名
	protected $user;
	//密码
	protected $pwd;
	//字符集
	protected $charset;
	//库名字
	protected $dbName;
	//表名字
	protected $table = 'user';
	//表前缀
	protected $prefix;
	//字段
	protected $fields;
	//选项
	protected $options;
	//sql语句
	protected $sql;
	
	
	public function __construct($config = null)
	{
		if (is_null($config)) {
			$config = $GLOBALS['config'];
		}
		$this->host = $config['DB_HOST'];
		$this->user = $config['DB_USER'];
		$this->pwd = $config['DB_PWD'];
		$this->charset = $config['DB_CHARSET'];
		$this->dbName = $config['DB_NAME'];
		$this->prefix = $config['DB_PREFIX'];
		
		//数据库链接
		$this->link = $this->connect();
		
		
		//获取表名字
		$this->table = $this->getTable();
		//var_dump($this->table);
		
		//字段
		$this->fields = $this->getFields();
		
	}
	
	//封装最大值
	public function max($fields)
	{
		if (empty($fields)) {
			$fields = $this->fields['_pk'];
		}
		
		$sql = "SELECT MAX($fields) as m FROM  $this->table";
		//echo $sql;
		$data = $this->query($sql);
		
		return $data[0]['m'];
		
		
	}
	//最小值
	public function min($fields)
	{
		if (empty($fields)) {
			$fields = $this->fields['_pk'];
		}
		
		$sql = "SELECT MIN($fields) as m FROM  $this->table";
		//echo $sql;
		$data = $this->query($sql);
		
		return $data[0]['m'];
		
		
	}
	//求和
	public function sum($fields)
	{
		if (empty($fields)) {
			$fields = $this->fields['_pk'];
		}
		
		$sql = "SELECT SUM($fields) as m FROM  $this->table";
		//echo $sql;
		$data = $this->query($sql);
		
		return $data[0]['m'];
		
		
	}
	//求总数
	public function count($fields)
	{
		if (empty($fields)) {
			$fields = $this->fields['_pk'];
		}
		
		$sql = "SELECT COUNT($fields) as m FROM  $this->table";
		//echo $sql;
		$data = $this->query($sql);
		
		return $data[0]['m'];
		
		
	}
	//求平均值
	public function avg($fields)
	{
		if (empty($fields)) {
			$fields = $this->fields['_pk'];
		}
		
		$sql = "SELECT AVG($fields) as m FROM  $this->table";
		//echo $sql;
		$data = $this->query($sql);
		
		return $data[0]['m'];
		
		
	}
	
	
	//处理字段
	
	protected function getFields()
	{
		$cacheFile = 'cache/' . $this->table . '.php';
		if (file_exists($cacheFile)) {
			return include $cacheFile;
		} else {
			
			$sql = 'DESC ' . $this->table;
		
			$data = $this->query($sql);
			
			//var_dump($data);
			
			$fields = [];
			
			foreach ($data as $key => $value) {
				$fields[] = $value['Field'];
				
				if ($value['Key'] == 'PRI') {
					$fields['_pk'] = $value['Key'];
				}
			}
			
			//var_dump($fields);
			$string = "<?php \n return " . var_export($fields , true) . ";?>";
			
			//var_dump($string);
			
			file_put_contents('cache/' . $this->table . '.php' , $string);
			return $fields;
		}
		
		
		
	}
	//删除 
	public function del()
	{
		$sql = 'DELETE FROM %TABLE% %WHERE%';
		$sql = str_replace(
			array('%TABLE%' , '%WHERE%'),
			array(
				$this->parseTable(),
				$this->parseWhere()
			),
			$sql
		);
		
		return $this->exec($sql , true);
	}
	//修改
	public function update($data)
	{
		if (!is_array($data)) {
			return false;
		}
		//update table set username = @@@@@ , password = ### where ##=****
		
		$sql = 'UPDATE %TABLE% SET %SET% %WHERE%';
		
		$sql = str_replace(
			array('%TABLE%' , '%SET%' , '%WHERE%'),
			array(
				$this->parseTable(),
				$this->parseSet($data),
				$this->parseWhere()
			),
			$sql
		);

		$result = $this->exec($sql , true);
		return $result;
	}
	//处理update set值
	protected function parseSet($data)
	{
		//var_dump($data);
		//name = 'niuxin2',time = '&&&&'
		$string = '';
		
		foreach ($data as $key => $value) {
			$string .= $key .'='. "'$value',";
		}
		
		return rtrim($string , ',');
		
	}
	
	//添加
	public function insert($data)
	{
	//var_dump($data);
		if (!is_array($data)) {
			return false;
		}
		
		$sql = 'INSERT INTO %TABLE% (%FIELDS%) VALUES(%VALUES%)';
		
		$sql = str_replace(
			array('%TABLE%' , '%FIELDS%' , '%VALUES%'),
			array(
				$this->parseTable(),
				$this->parseAddFields(array_keys($data)),
				$this->parseAddValues(array_values($data))
			),
			$sql
		);
		return $this->exec($sql);
		
	}
	//执行sqlyuj
	protected function exec($sql , $bool = null)
	{
		if ($bool) {
			$result = mysqli_query($this->link , $sql);
		
			if ($result) {
				return mysqli_affected_rows($this->link);
			} else {
				return false;
			}
		} else {
			
			$result = mysqli_query($this->link , $sql);
		
			if ($result) {
				return mysqli_insert_id($this->link);
			} else {
				return false;
			}
		}
		
	}
	//出来添加的值
	protected function parseAddValues($data)
	{
		//var_dump($data);
		$string = '';
		
		foreach ($data as $val) {
			$string .= '\''.$val.'\',';
		}
		return rtrim($string , ',');
		
	}
	//出来添加的字段
	protected function parseAddFields($keys)
	{
		
		return join(',' , $keys);
	}
	
	//查询
	public function select()
	{
		//var_dump($this->options);
		$sql = 'SELECT %FIELDS% FROM %TABLE% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT% ';
		
		//var_dump($this->options);
		
		$sql = str_replace(
			array('%FIELDS%','%TABLE%' , '%WHERE%' , '%GROUP%' , '%ORDER%' , '%LIMIT%' , '%HAVING%'),
			array(
				$this->parseFields(isset($this->options['fields']) ? $this->options['fields'] : null),
				$this->parseTable(),
				$this->parseWhere(),
				$this->parseGroup(),
				$this->parseOrder(),
				$this->parseLimit(),
				$this->parseHaving()
			),
			$sql
		);

		$data = $this->query($sql);
		return $data;
	}
	//处理limit
	protected function parseLimit()
	{
		$limit = '';
		
		if (empty($this->options['limit'])) {
			$limit = '';
		} else {
			if (is_string($this->options['limit'][0])) {
				$limit = 'LIMIT ' . $this->options['limit'][0];
			}
			
			if (is_array($this->options['limit'][0])) {
				$limit = 'LIMIT ' . join(',' , $this->options['limit'][0]);
			}
			
		}
		return $limit;
	}
	//处理having
	protected function parseHaving()
	{
		$having = '';
		
		if (empty($this->options['having'])) {
			$having = '';
		} else {
			$having = 'HAVING ' . $this->options['having'][0];
		}
		
		return $having;
	}
	//处理排序
	protected function parseOrder()
	{
		$order = '';
		
		if (empty($this->options['order'])) {
			$order = '';
		} else {
			$order = 'ORDER BY ' . $this->options['order'][0];
		}
		
		return $order;
	}
	//处理分组
	protected function parseGroup()
	{
		$group = '';
		
		if (empty($this->options['group'])) {
			$group = '';
		} else {
			$group = 'GROUP BY ' . $this->options['group'][0];
		}
		
		return $group;
	}
	//处理where条件
	protected function parseWhere()
	{
		$where = '';
		
		if (empty($this->options['where'])) {
			$where = '';
		} else {
			$where = 'WHERE ' . $this->options['where'][0];
		}
		
		return $where;
	}
	
	//处理表的问题
	protected function parseTable()
	{
		$table = '';
		
		if (isset($this->options['table'])) {
			$table = $this->prefix . $this->options['table'][0];
		} else {
			$table = $this->table;
		}
		
		return $table;
	}
	
	//处理字段的问题
	protected function parseFields($options)
	{
		//var_dump($options);
		//return '这是我替换好的字段';
		$fields = '';
		if (empty($options)) {
			$fields = '*';
		} else {
			if (is_string($options[0])) {
				$fields = explode(',' , $options[0]);
				
				//var_dump($fields);
				$tmpArr = array_intersect($fields , $this->fields); // select id , name ,ip form ////
				
				$fields = join(',' , $tmpArr);
			
			}
			
			if (is_array($options[0])) {
					
				$fields = join(',' , array_intersect($options[0] , $this->fields));
			}
		}
		return $fields;
	}
	
	
	//通过call方法实现连贯操作
	public function __call($func , $args)
	{
		
		//var_dump($func , $args);
		
		if (in_array($func , ['fields' , 'table' , 'where' , 'group' , 'order' , 'limit' , 'having'])) {
			$this->options[$func] = $args;
			return $this;
		} else if(strtolower(substr($func , 0 , 5)) == 'getby') {
			
			$fields = strtolower(substr($func , 5));
			
			return $this->getBy($fields , $args[0]);
			
			
		} else {
			exit('你丫的瞎搞毛线，俺们不支持这个方法嘞');
		}
	}
	//处理getBy
	protected function getBy($fields , $args)
	{
		$sql = "select * from $this->table where $fields = '$args'";
		
		return $this->query($sql);
	}
	//发送sql
	protected function query($sql)
	{
		$result = mysqli_query($this->link , $sql);
		
		//var_dump($result);
		$data = [];
		if ($result) {
			while ($rows = mysqli_fetch_assoc($result)) {
				//var_dump($rows);
				$data[] = $rows;
			}
		} else {
			return false;
		}
		
		return $data;
	}
	
	
	//处理表名字
	protected function getTable()
	{
		//两种情况 如果给了默认值和没有给默认值的情况
		$table = '';
		if (isset($this->table)) {
			$table = $this->prefix . $this->table;
		} else {
			
			$table = $this->prefix . strtolower(substr(get_class($this) , 0 ,-5));
			
		}
		
		return $table;
	}
	
	//处理数据库链接
	protected function connect()
	{
		$link = mysqli_connect($this->host , $this->user , $this->pwd);
		
		if (!$link) {
			exit('数据库链接失败');
		}
		
		mysqli_set_charset($link , $this->charset);
		
		mysqli_select_db($link , $this->dbName);
		
		return $link;
	}
}



