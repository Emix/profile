<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Image;
use app\models\ObjectModel;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PngConvertController extends Controller
{
	public function init() {
		ob_start();
		parent::init(); // TODO: Change the autogenerated stub
	}

	/**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'beginning import images')
    {
        $dir = Yii::getAlias('@app/images/store');
        self::e('Получаем данные из БД');
    	$images = Image::find()->asArray()->all();

    	$count = 0 ;

    	foreach ($images as $image){
    	    $file = $dir . DIRECTORY_SEPARATOR . $image['filePath'];

    	    if(file_exists($file) && "image/png" === mime_content_type($file)){
                $jpegFile = self::convert($file);
                $baseName = basename($jpegFile);

                $oldBaseName = basename($file);
                $newfile = str_replace($oldBaseName, $baseName, $file);
                if(file_exists($newfile)){
                    $newfile = str_replace(Yii::getAlias('@app/images/store') . DIRECTORY_SEPARATOR, '', $newfile);


                    $updateMode = Image::findOne($image['id']);
                    $updateMode->filePath = $newfile;
                    if($updateMode->save()){
                        unlink($file);
                    }
                    $count++;
                    self::e('Image ID #'.$updateMode->id . ' конвертирован');
                }
            }
        }


    	self::e('Конвертация закончена');
    	self::e('Всего обработано '.sizeof($images).' картинок из них '.$count .' успешно конвертированы в JPG');

        return ExitCode::OK;
    }

    static public function convert($filePath){
        $image = imagecreatefrompng($filePath);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file

        $filePath = str_replace('.png', '', $filePath);
        $newFilePath = $filePath . ".jpg";

        if(file_exists($newFilePath)){
            unlink($newFilePath);
        }

        imagejpeg($bg, $newFilePath, $quality);
        imagedestroy($bg);

        return $newFilePath;
    }

	static private function e($msg = ''){
		if(is_array($msg)){
			echo print_r($msg, 1) . PHP_EOL;

		}else{
			echo $msg .PHP_EOL;
		}

		ob_flush();
	}
}
