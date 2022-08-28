<?php

require_once _PS_MODULE_DIR_ . 'webstrum/controllers/front/Helpers/DBHelper.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

class WebstrumAjaxManagerModuleFrontController extends ModuleFrontController
{
    private const WEBSTRUM_TABLE = 'webstrum_gallery_photos';
    private const TEMP_IMG_PATH = 'img/tmp/';
    private const WEBSTRUM_IMG_PATH = 'img/webstrum/';
    private const WEBSTRUM_SMALL_THUMB_PREFIX = "thumb-small";
    private const CONFIG_PHOTOS_LIMIT = 'WEBSTRUM_MAX_PHOTOS_LIMIT';
    private const CONFIG_MAX_PHOTO_FILE_SIZE = 'WEBSTRUM_MAX_PHOTO_FILE_SIZE';

    public $controller_name = 'apg';
    public $module_name = 'webstrum'; 
    private $fileSystem;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }
    public function displayAjax()
    {
        if(Tools::isSubmit('uploadFiles')){
            $this->uploadPhoto($_FILES['file']);
        }
        if(Tools::isSubmit('getPhotos')) {
            $this->getPhotos(Tools::getValue('webstrumProductId'));
        }
        if(Tools::isSubmit('deletePhoto')) {
            $this->deletePhoto(Tools::getValue('photoId'));
        }
    }
    public function deletePhoto($photoId)
    {
        $productId = DBHelper::getValue("product_id", "id", $photoId);
        $photoUrl = DBHelper::getValue("photo_url", "id", $photoId);
        if(!$photoUrl)
            die(json_encode(array('status' => 'error', 'message' => 'File does not exist!')));
        DBHelper::delete('id', $photoId);
        try {
            $this->filesystem->remove('img/webstrum/'.$productId.'/'.$photoUrl);
            $this->filesystem->remove('img/webstrum/'.$productId.'/'.self::WEBSTRUM_SMALL_THUMB_PREFIX.'-'.$photoUrl);
        }
        catch(IOException $e){
            die(json_encode(array('status' => 'error', 'message' => $e->getMessage())));
        }
        die(json_encode(array('status' => 'success', 'message' => 'Photo has been deleted!')));
    }
    
    public function getPhotos($productId)
    {
        $result = DBHelper::executeS("*", "product_id", $productId);
        die(json_encode(array('status' => 'success', 'photos' => $result)));
    }
    public function uploadPhoto($file)
    {
        $productId = Tools::getValue('webstrumProductId');
        $max_photos_limit = Configuration::get(self::CONFIG_PHOTOS_LIMIT);
        $max_photo_file_size = Configuration::get(self::CONFIG_MAX_PHOTO_FILE_SIZE);
        $count = DBHelper::getValue("COUNT(*)", "product_id", $productId, "Count");
        if($count >= $max_photos_limit)
            die(json_encode(array('status' => 'error', 'message' => 'Max photos limit reached!')));
        if(!empty($file)){   
            $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
            $path = self::WEBSTRUM_IMG_PATH.$productId.'/';
            $file_name = time().'.'.$ext; 
            $temp = $file['tmp_name'];

            if ($error = ImageManager::validateUpload($file, $max_photo_file_size*1000, null)) {
                die(json_decode(array('status' => 'error', 'message' => $error)));
            } 
            try {
                if(!$this->filesystem->exists($path)){
                    $this->filesystem->mkdir($path, 0755);
                }
                $this->filesystem->copy($temp, $path.$file_name);
                $this->filesystem->remove($temp);
                $this->makeThumbnail($path, $file_name, self::WEBSTRUM_SMALL_THUMB_PREFIX, 125);
            }
            catch(IOException $e){
                die(json_encode(array('status' => 'error', 'message' => $e->getMessage())));
            }
            
            DBHelper::insert(array('photo_url' => $file_name, 'product_id' => $productId));
            $result = DBHelper::getRow("*", "photo_url", '"'.$file_name.'"');

            die(json_encode(array('status' => 'success', 'photo' => $result, 'productId' => $productId)));
        }
    }
    private function makeThumbnail(string $originPath, string $originFileName, string $append = "thumb-small", int $size) 
    {
        $tmpFileName = $append.'-'.$originFileName;
        ImageManager::thumbnail($originPath.$originFileName, $tmpFileName, $size);
        $this->filesystem->copy(self::TEMP_IMG_PATH.$tmpFileName, $originPath.$tmpFileName);
        $this->filesystem->remove(self::TEMP_IMG_PATH.$tmpFileName);
    }
}