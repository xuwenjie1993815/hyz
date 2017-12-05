<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: hkj  <584745119@qq.com>
// +----------------------------------------------------------------------

    
    $QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'/phpqrcode/';
    
    // Required libs
    
    include $QR_BASEDIR."qrconst.php";
    include $QR_BASEDIR."qrconfig.php";
    include $QR_BASEDIR."qrtools.php";
    include $QR_BASEDIR."qrspec.php";
    include $QR_BASEDIR."qrimage.php";
    include $QR_BASEDIR."qrinput.php";
    include $QR_BASEDIR."qrbitstream.php";
    include $QR_BASEDIR."qrsplit.php";
    include $QR_BASEDIR."qrrscode.php";
    include $QR_BASEDIR."qrmask.php";
    include $QR_BASEDIR."qrencode.php";

/**
 * 二维码生成
 * @author    hkj  <584745119@qq.com>
 */
class Phpqrcode {

    /**
     * 取得图像信息
     * @static
     * @access public
     * @param string $data 二维码内容
     * @param string $water_url  水印图片
     * @param string $level  'L','M','Q','H'
     * @param string $size   大小  1到10
     * @return mixed
     */

    static public function set_code($data='',$water_url, $level='H', $size='10') {
    
        if(empty($data) || trim($data)==''){
            return false;
        }
        if(empty($water_url)){
           $water_url="./Uploads/logg.png";

        }
        $errorCorrectionLevel='M';
        if (in_array($level, array('L','M','Q','H')))
          $errorCorrectionLevel = $level;

        $matrixPointSize = 10;
        if ($size){
            $matrixPointSize = min(max((int)$size, 1), 10);
        }
        
        $PNG_TEMP_DIR='./Uploads';
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR);
        }

        $PNG_TEMP_DIR.='/Code_cache';  
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR);
        }
        
        //$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        $filename = $PNG_TEMP_DIR.'/'.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        
        if (file_exists($filename)){//存在返回
            return substr($filename, 1);
        }
       
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        // QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize, 2);

        //给图添加水印
        if(file_exists($filename) && file_exists($water_url)){
            //给图添加水印, Image::water('原文件名','水印图片地址')
            self::water($filename, $water_url);
        }
        
        return substr($filename, 1);
    }
    
    static public function set_pay_code($data='', $pay_sn, $level='H', $size='10') {
    
        if(empty($data) || trim($data)==''){
            return false;
        }
        
        $errorCorrectionLevel='M';
        if (in_array($level, array('L','M','Q','H')))
          $errorCorrectionLevel = $level;

        $matrixPointSize = 10;
        if ($size){
            $matrixPointSize = min(max((int)$size, 1), 10);
        }
        
        $PNG_TEMP_DIR='./Uploads';
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR);
        }

        $PNG_TEMP_DIR.='/pay_code/'.date('Y').'/'.date('md');  
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR, 777, true);
        }
        
        $filename = $PNG_TEMP_DIR.'/'.$pay_sn.'.png';
        
        if (file_exists($filename)){//存在返回
            return substr($filename, 1);
        }
       
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        // QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize, 2);
        
        return substr($filename, 1);
    }
    
    /**
     * 取得图像信息
     * @static
     * @access public
     * @param string $image 图像文件名
     * @return mixed
     */

    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 为图片添加水印
     * @static public
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param string $$savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
     * @return void
     */
    static public function water($source, $water, $savename=null, $alpha=100) {
        //检查文件是否存在
        if (!file_exists($source) || !file_exists($water))
            return false;

        //图片信息
        $sInfo = self::getImageInfo($source);
        $wInfo = self::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
            return false;

        //建立图像
        $sCreateFun = "imagecreatefrom" . $sInfo['type'];
        $sImage = $sCreateFun($source);
        $wCreateFun = "imagecreatefrom" . $wInfo['type'];
        $wImage = $wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐 
        ///$sInfo["height"] - $wInfo["height"]
        //$sInfo["width"] - $wInfo["width"]
        //图像位置,默认为居中 
        $posY = ($sInfo["height"] - $wInfo["height"])/2;
        $posX = ($sInfo["width"] - $wInfo["width"])/2;

        //生成混合图像
        //imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);
         //生成混合图像 支持png
          self::imagecopymerge_alpha($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);   
        //输出图像
        $ImageFun = 'Image' . $sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if (!$savename) {
            $savename = $source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
    }
    static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        $opacity=$pct;
        // getting the watermark width
        $w = imagesx($src_im);
        // getting the watermark height
        $h = imagesy($src_im);
             
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // copying that section of the background to the cut
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        // inverting the opacity
        //$opacity = 100 - $opacity;
             
        // placing the watermark now
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity);
    }



}
