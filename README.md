# PDFPreview
# Typecho文章导出插件
# 借助 PHP 库 mpdf 生成 PDF
使用方法：下载该插件放到插件目录下，
该插件依赖 mPDF 来生成 PDF，需要安装 mPDF。可以使用 Composer 来安装库：
终端进入插件目录 usr/plugins/PDFPreview
运行以下命令来安装 mPDF：
composer require mpdf/mpdf
这会在 PDFPreview 目录中生成一个 vendor 文件夹，该文件夹包含 mPDF 所需的依赖文件。
安装成功后可以看到插件目录下新添了几个文件：

![image](https://github.com/user-attachments/assets/7d1c958a-377d-4d37-a807-b99dffa0940b)

然后登录 Typecho 后台，进入插件管理页面。
在插件列表中找到 PDFPreview 插件，点击“启用” 即可。在前端页面右下角出会出现导出文章按钮。
导出的pdf的截图示例：
![image](https://github.com/user-attachments/assets/fccee451-f108-43cc-894e-9da644714c1c)



