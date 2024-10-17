<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 基于mpdf导出pdf的插件:
 * 目前生成的pdf在主流pdf阅读器中目录不能跳转
 * 
 * 
 * @package PDFPreview
 * @author Bruce Kevin Chen
 * @version 1.0.1
 * @link https://www.chendk.info
 */
class PDFPreview_Plugin implements Typecho_Plugin_Interface
{
    // 插件激活时调用
    public static function activate()
    {

        // 将按钮添加到文章页面和指定的独立页面
        Typecho_Plugin::factory('Widget_Archive')->singleHandle = array('PDFPreview_Plugin', 'addPDFPreviewButton');
        // 检查 PDF 预览请求
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('PDFPreview_Plugin', 'checkPDFPreviewRequest');
        return _t('PDF 预览插件已激活');
    }
    
    // 插件停用时调用
    public static function deactivate()
    {
        return _t('PDF 预览插件已禁用');
        
    }

    // 插件配置面板（如果需要，可以添加配置选项）
    public static function config(Typecho_Widget_Helper_Form $form) {}

    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    // 卸载插件时调用
    public static function uninstall() {}

    // 在文章页面和指定的独立页面（如 about 页面）添加悬浮 PDF 预览图标按钮，带悬停提示
    public static function addPDFPreviewButton($archive)
    {
        // 检查是否为 PDF 预览请求，如果是，则不插入悬浮按钮
        if ($archive->request->get('pdf_preview') == 1) {
            // 在生成 PDF 时不加载悬浮按钮
            return;
        }
    
        // 判断是否是文章页面或者是独立页面
        if ($archive->is('post') || ($archive->is('page') && $archive->slug == 'about')) {
            // 构造当前页面的 PDF 预览 URL
            $currentUrl = $archive->permalink . '?pdf_preview=1';
            // 使用 JavaScript 动态插入悬浮按钮和提示信息
            echo '<script type="text/javascript">
                window.addEventListener("load", function() {
                    // 插入悬浮按钮，保持固定大小
                    var pdfButton = document.createElement("a");
                    pdfButton.href = "' . htmlspecialchars($currentUrl) . '";
                    pdfButton.target = "_blank";
                    pdfButton.title = "导出PDF";
                    pdfButton.style.position = "fixed";
                    pdfButton.style.bottom = "85px";
                    pdfButton.style.right = "25px";
                    pdfButton.style.color = "white";
                    pdfButton.style.padding = "15px";
                    pdfButton.style.borderRadius = "50%";
                    pdfButton.style.textDecoration = "none";
                    pdfButton.style.zIndex = "1000";
    
                    var img = document.createElement("img");
                    img.src = "https://www.chendk.info/usr/themes/jasmine/pictures/ExportPDF.svg";
                    img.alt = "PDF 预览";
                    img.style.width = "25px";
                    img.style.height = "25px";
    
                    pdfButton.appendChild(img);
                    document.body.appendChild(pdfButton);
    
                    // 插入提示信息，使用相对单位
                    var pdfToast = document.createElement("div");
                    pdfToast.textContent = "右下角可导出PDF";
                    pdfToast.style.position = "fixed";
                    pdfToast.style.bottom = "55vh";
                    pdfToast.style.right = "50vw";
                    pdfToast.style.backgroundColor = "rgba(0, 0, 0, 0.6)";
                    pdfToast.style.color = "white";
                    pdfToast.style.padding = "1.5vh 2vw";
                    pdfToast.style.borderRadius = "5px";
                    pdfToast.style.fontSize = "1.5vh";
                    pdfToast.style.zIndex = "1001";
                    pdfToast.style.opacity = "0";
                    pdfToast.style.transition = "opacity 0.5s";
    
                    document.body.appendChild(pdfToast);
    
                    // 显示提示信息并在几秒钟后自动隐藏
                    setTimeout(function() {
                        pdfToast.style.opacity = "1";
                    }, 500); // 500ms 后显示提示
    
                    setTimeout(function() {
                        pdfToast.style.opacity = "0";
                        setTimeout(function() {
                            pdfToast.remove();
                        }, 500); // 500ms 后彻底移除提示框
                    }, 3000); // 3秒后隐藏提示
                });
            </script>';

        }
    }
    
    public static function checkPDFPreviewRequest($archive)
    {
        // 如果是 PDF 预览请求，则生成 PDF
        if ($archive->request->get('pdf_preview') == 1 && $archive->is('single')) {
            self::generatePDF($archive);
            exit;  // 生成完 PDF 后停止进一步输出
        }
    }

    // public static function checkPDFPreviewRequest($archive)
    // {
    //     // 检查是否为 PDF 预览请求
    //     if ($archive->request->get('pdf_preview') == 1 && $archive->is('single')) {
    //         // 仅在生成 PDF 时使用输出缓冲区
    //         ob_start();
    //         self::generatePDF($archive);
    //         $output = ob_get_clean();
    //         echo $output;
    //     } else {
    //         // 对于其他页面，不使用缓冲区，确保正常输出
    //         return;
    //     }
    // }


    // 生成 PDF 文件，带有书签目录和跳转功能

    private static function generatePDF($archive)
    {
        require_once __DIR__ . '/vendor/autoload.php';
    
        // 获取文章标题和内容
        $title = $archive->title;
        $content = $archive->content;
        $author = $archive->author->screenName;
        $date = date('Y-m-d');  // 当前日期
    
        // 实例化 mPDF，设置自定义的临时文件目录和字体
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => __DIR__ . '/tmp',  // 指定临时文件目录
            'default_font' => 'noto',        // 设置默认字体为 Noto Sans SC
            'format' => 'A4',                // 设置纸张大小
            'margin_top' => 30,              // 上边距
            'margin_bottom' => 30,           // 下边距
            'margin_left' => 15,             // 左边距
            'margin_right' => 15             // 右边距
        ]);
    
        // 加载中文字体文件
        $mpdf->fontdata['noto'] = [
            'R' => __DIR__ . '/fonts/NotoSansSC-Regular.ttf',  // 普通字体文件路径
            'B' => __DIR__ . '/fonts/NotoSansSC-Bold.ttf',     // 粗体字体文件路径
        ];
        $mpdf->SetDefaultFont('noto');  // 设置默认字体为 Noto Sans SC
    
        // 设置页眉（标题）和下方的分割线
        $mpdf->SetHTMLHeader('
        <div style="text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 2px;">' . htmlspecialchars($title) . '</div>
        <hr style="border: 0; height: 2px; background-color: #000; margin-top: 0;">
        ');
    
        // 设置页脚（日期）
        $mpdf->SetHTMLFooter('<div style="text-align: center; font-size: 10pt;">' . $date . '</div>');
    
        // 设置水印（作者名称）
        $mpdf->SetWatermarkText($author, 0.1);  // 设置水印透明度，0.1 表示轻微透明
        $mpdf->showWatermarkText = true;        // 启用水印显示
    
        // 设置 CSS 样式
        $css = '
        <style>
        body {
            font-family: noto, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }
        h1 {
            font-size: 24pt;
            margin-bottom: 20px;
            text-align: center;
            color: #FF5733;
        }
        h2, h3, h4, h5, h6 {
            font-weight: bold;
            color: #007bff;
        }
        p {
            margin-bottom: 12px;
            text-align: justify;
        }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e1e1e8;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
        }
        code {
            color: #e83e8c;
            background-color: #f8f9fa;
            padding: 3px 5px;
            border-radius: 3px;
        }
        blockquote {
            border-left: 3px solid #007bff;
            margin: 10px 0;
            padding-left: 10px;
            color: #555;
        }
        img {
            display: block;
            margin: 10px auto;
            max-width: 100%;
        }
        </style>
        ';
    
        // 插入 CSS 样式
        $mpdf->WriteHTML($css);
    
        // 处理 HTML 内容，解析其中的 GIF 图片并生成书签目录
        $html = '<h1>' . htmlspecialchars($title) . '</h1>' . $content;
    
        // 定义一个数组来追踪当前的标题层次
        $currentLevel = 0;
        $bookmarkCounter = 0;  // 书签计数器，确保每个书签有唯一的 ID
    
        // 为每个标题生成书签，并处理 GIF 动图
        $html = preg_replace_callback('/<h([1-6])>(.*?)<\/h\1>|<img.*?src=["\'](.*?)["\'].*?>/', function ($matches) use ($mpdf, &$currentLevel, &$bookmarkCounter) {
            if (isset($matches[3])) {
                // 处理图片（GIF 动图）
                $imageSrc = $matches[3];
    
                // 判断是否是 GIF 格式的图片
                if (preg_match('/\.gif$/i', $imageSrc)) {
                    // 如果是 GIF 动图，则生成带链接的占位符图片
                    $firstFrameImg = self::getGifFirstFrame($imageSrc);  // 获取 GIF 的第一帧
                    return '<a href="' . htmlspecialchars($imageSrc) . '" target="_blank">
                                <img src="' . htmlspecialchars($firstFrameImg) . '" alt="点击查看动态图" />
                            </a>';
                }
    
                // 如果不是 GIF，则正常显示图片
                return $matches[0];
            } else {
                // 处理标题并生成书签
                $level = intval($matches[1]);  // 获取标题的层级（1 到 6）
                $text = strip_tags($matches[2]);  // 获取标题文本
    
                // 如果当前标题层级大于上一个层级，则增加书签层级
                if ($level > $currentLevel) {
                    $currentLevel = $level;
                } elseif ($level < $currentLevel) {
                    // 如果当前标题层级小于上一个层级，重置层级
                    $currentLevel = $level;
                }
    
                $link = $mpdf->AddLink();  // 创建一个内部链接
                $bookmarkId = 'bookmark-' . ++$bookmarkCounter;  // 为书签生成唯一的 ID
                $mpdf->Bookmark($text, $currentLevel - 1, null, null, $link);  // 为每个标题创建书签，书签层级从 0 开始
                $mpdf->SetLink($link);  // 设置该链接指向当前页面位置
                return '<h' . $level . ' id="' . $bookmarkId . '">' . $matches[2] . '</h' . $level . '>';
            }
        }, $html);
    
        // 将处理后的 HTML 内容写入 PDF
        $mpdf->WriteHTML($html);
    
        // 输出 PDF 到浏览器并触发下载
        $mpdf->Output($title . '.pdf', 'D');
        exit;
    }
    
    // 获取 GIF 的第一帧
    private static function getGifFirstFrame($gifUrl)
    {
        // 使用 PHP 的 GD 库来提取 GIF 的第一帧并保存为临时文件
        $image = imagecreatefromgif($gifUrl);
        $outputPath = __DIR__ . '/tmp/first_frame.png';
        imagepng($image, $outputPath);
        imagedestroy($image);
        return $outputPath;
    }

}
