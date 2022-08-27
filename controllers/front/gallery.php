<?php

require_once _PS_MODULE_DIR_ . 'webstrum/controllers/front/Helpers/DBHelper.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

class WebstrumGalleryModuleFrontController extends ModuleFrontController
{
    const WEBSTRUM_TABLE = 'webstrum_gallery_photos';
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
            try{
                $file = $this->uploadPhoto($_FILES['file']);
            }
            catch(Exception $e){
                echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
                return;
            }
            echo json_encode($file);
        }
        if(Tools::isSubmit('getPhotos')){
            $productId = Tools::getValue('webstrumProductId');
            echo json_encode($this->getPhotos($productId));
        }
        if(Tools::isSubmit('deletePhoto')){
            $photoId = Tools::getValue('photoId');
            echo json_encode($this->deletePhoto($photoId));
        }
    }
    public function deletePhoto($photoId)
    {
        $photoUrl = DBHelper::getValue("photo_url", "id", $photoId);
        if(!$photoUrl)
            return array('status' => 'error', 'message' => 'File does not exist!');
        $this->filesystem->remove('img/webstrum/'.$photoUrl);
        DBHelper::delete('id', $photoId);
        return array('status' => 'success', 'message' => 'Photo has been deleted!');
    }
    
    public function getPhotos($productId)
    {
        $result = DBHelper::executeS("*", "product_id", $productId);
        return array('status' => 'success', 'photos' => $result);
    }
    public function uploadPhoto($file)
    {
        $productId = Tools::getValue('webstrumProductId');
        $max_photos_limit = Configuration::get('WEBSTRUM_MAX_PHOTOS_LIMIT');
        $max_photo_file_size = Configuration::get('WEBSTRUM_MAX_PHOTO_FILE_SIZE');
        $count = DBHelper::getValue("COUNT(*)", "product_id", $productId, "Count");
        if($count >= $max_photos_limit)
            return array('status' => 'error', 'message' => 'Max photos limit reached!');
        if(!empty($file)){   
            $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
            $file_name = $productId.'-'.time().'.'.$ext; 
            $temp = $file['tmp_name'];

            if ($error = ImageManager::validateUpload($file, $max_photo_file_size*1000, null)) {
                return array('status' => 'error', 'message' => $error);
            } 
            
            if(!$this->filesystem->exists('img/webstrum')){
                $this->filesystem->mkdir('img/webstrum', 0755);
            }
            $this->filesystem->copy($temp, 'img/webstrum/'.$file_name);
            $this->filesystem->remove($temp);
            
            DBHelper::insert(array('photo_url' => $file_name, 'product_id' => $productId));
            $result = DBHelper::getRow("*", "photo_url", '"'.$file_name.'"');
            return array('status' => 'success', 'photo' => $result);
          }
      }
    
}