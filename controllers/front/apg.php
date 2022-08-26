<?php
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class WebstrumApgModuleFrontController extends ModuleFrontController
{
    public $controller_name = 'apg';
    public $module_name = 'webstrum'; 
    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }
    public function displayAjax()
    {
        if(Tools::isSubmit('uploadFiles')){
          $file = $this->makeUpload($_FILES['file']);
          echo json_encode($file);
        }
        if(Tools::isSubmit('getExistingPhotos')){
            echo json_encode($this->getExistingPhotos(Tools::getValue('webstrumProductId')));
        }
    }
    public function makeUpload($file){
        $productId = Tools::getValue('webstrumProductId');
        $max_photos_limit = Configuration::get('WEBSTRUM_MAX_PHOTOS_LIMIT');
        $query = "SELECT COUNT(*) as Count FROM "._DB_PREFIX_."webstrum_gallery_photos WHERE `product_id`='".$productId."'";
        $count = Db::getInstance()->getValue($query);
        if($count >= $max_photos_limit)
            return array('status' => 'error', 'message' => 'Max photos limit reached!');
        if(!empty($file)){   
            $file_name = $file['name'];
            $temp = $file['tmp_name'];
            if ($error = ImageManager::validateUpload($file)) {
                return $error;
            } 
            else {
                $ext = substr($file_name, strrpos($file_name, '.') + 1);
                $file_name = $productId.'-'.time().'.'.$ext; 
            if (!file_exists('img/webstrum')) {
                mkdir('img/webstrum', 0777, true);
            }
            if (!move_uploaded_file($temp, 'img/webstrum/'.$file_name)) {
                return array('status' => 'error', 'message' => 'An error occured while attempting to upload to file!');
            }
            $insertData = array(
                'photo_url' => $file_name,
                'product_id' => $productId
            );
            Db::getInstance()->insert("webstrum_gallery_photos", $insertData);
            return array('status' => 'success', 'filename' => $file_name);
          }
        }
      }
    public function getExistingPhotos($productId){
        $query = "SELECT `photo_url` FROM "._DB_PREFIX_."webstrum_gallery_photos WHERE `product_id`='".$productId."'";
        $result = Db::getInstance()->executeS($query);
        return $result;
    }
}