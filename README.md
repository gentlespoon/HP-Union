## HP-Union

2010-2012年里建的站 后来忙高考就荒废了

现在有空想重新做起来 反正工作之余做着玩

不知道最终会做成什么 和开发工坊讨论决定

比较可能做成个论坛吧 因为以前就是个论坛


#### 安装方法
	
~~~~
$ git clone https://github.com/gentlespoon/GsMVC.git
$ mv GsMVC public_html
$ cd public_html
$ git clone https://github.com/gentlespoon/HP-Union.git
$ rm app -rf
$ mv HP-Union app
~~~~

设置public_html目录为DOCUMENT_ROOT

复制`app/modules/00_config.default.php`并更名为`00_config.php`

在`00_config.php`文件中填写数据库信息


#### 更新服务器

直接push到repository即可

会自动触发webhook在服务器端进行git pull
