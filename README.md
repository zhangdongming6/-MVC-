# 轻量级别的MVC框架

## 目录结构

### app应用目录

​	Controller	控制器     

```php
需首字母大写，并在名称中添加“Controller”，如：ItemsController，CarsController
```

​	Model		模型

```
需首字母大写，，并在名称后添加“Model”，如：ItemModel，CarModel
```

​	Views		视图

```
就是用来存放你的HTML页面的
```

### bootstrap	启动目录

Psr4AutoLoad.php

```php
自动加载
```

Start.php

```php
启动文件、对路由进行配置
```

### cache	缓存目录

```php
数据的字段缓存
模板的缓存
```

### config	配置文件目录

```php
数据库的配置
缓存文件的配置
整个网站的路径配置
```

### public	公共目录

```php
在这里可以存放你的图片文件、css样式文件、js样式文件、字体文件等等
```

### vendor	第三方供应商   第三方扩展库

```php
在这里可以存放一些第三方的扩展库以及API接口等等
```

