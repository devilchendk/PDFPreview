# PDFPreview
# Typecho文章导出插件
# 借助 PHP 库 mpdf 生成 PDF
本来想借助强大的wkhtmltopdf来完成的，但是手动部分太多，坑也太多，最后导出效率还不及mpdf完成的，又改改改最后竟然做成了网页版文章内容预览（网页内只有文章内容）
太鸡肋了就中途放弃了，还想过通过链接的方式引入手动生成的pdf下载，但是这也太鸡肋了（😄）也放弃了（这个简单），中途还使用了个啥我给忘了，那个还要用手动命令把中文字体转换成自己的能使用的文件放入字体目录，
然后还要弄来弄去（其实英文都没这些乱码的烦恼），最后还是用我感觉第二强大的mpdf来完成了，自我感觉它不错。

# 使用方法：下载该插件放到插件目录下
该插件依赖 mPDF 来生成 PDF，需要安装 mPDF。可以使用 Composer 来安装库：
终端进入插件目录 usr/plugins/PDFPreview
运行以下命令来安装 mPDF：
composer require mpdf/mpdf
这会在 PDFPreview 目录中生成一个 vendor 文件夹，该文件夹包含 mPDF 所需的依赖文件。
安装成功后可以看到插件目录下新添了几个文件：

![image](https://github.com/user-attachments/assets/7d1c958a-377d-4d37-a807-b99dffa0940b)


然后登录 Typecho 后台，进入插件管理页面。
在插件列表中找到 PDFPreview 插件，点击“启用” 即可。在前端页面右下角出会出现导出文章按钮。
使用中文字体依旧有乱码情况请参照（就激活三个参数很简单）： 

https://cloud.tencent.com/developer/article/2162330

导出的pdf的截图示例：
![image](https://github.com/user-attachments/assets/fccee451-f108-43cc-894e-9da644714c1c)



