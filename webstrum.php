<?php

if (!defined('_PS_VERSION_'))
    exit();

require_once _PS_MODULE_DIR_ . 'webstrum/controllers/front/Helpers/DBHelper.php';

use Symfony\Component\Filesystem\Filesystem;

class Webstrum extends Module
{
    private const CONFIG_PHOTOS_LIMIT = 'WEBSTRUM_MAX_PHOTOS_LIMIT';
    private const CONFIG_MAX_PHOTO_FILE_SIZE = 'WEBSTRUM_MAX_PHOTO_FILE_SIZE';
    private const PRODUCT_GALLERY_TEMPLATE = '/views/templates/hook/productGallery.tpl';
    private const ADD_PRODUCT_GALLERY_TEMPLATE = '/views/templates/hook/additionalProductGallery.tpl';

    private $fileSystem;

    public function __construct()
    {
        $this->name = 'webstrum';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Gediminas Bubliauskas';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->controllers = array('apg'); 
        parent::__construct();
        $this->filesystem = new Filesystem();
        $this->displayName = $this->l('Webstrum qualification test', 'webstrum');
        $this->description = $this->l('Modulis skirtas Webstrum testui', 'webstrum');
        $this->confirmUninstall = $this->l('Ar norite pasalinti si moduli?', 'webstrum');
        
    }
    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);
        return parent::install() 
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('header')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->prepareConfig()
            && DBHelper::createDBTable()
            && Configuration::updateValue('webstrum', 'wlsdMpnDBn8');
    }
    public function prepareConfig(){
        return Configuration::updateValue(self::CONFIG_PHOTOS_LIMIT, 8)
            && Configuration::updateValue(self::CONFIG_MAX_PHOTO_FILE_SIZE, 2048);
    }
    //TODO: Create checkbox on uninstall, so users can choose if they want to delete img folder
    public function uninstall() 
    {
        if (!parent::uninstall() 
            || !Configuration::deleteByName('webstrum')
            || !Configuration::deleteByName(self::CONFIG_PHOTOS_LIMIT)
            || !Configuration::deleteByName(self::CONFIG_MAX_PHOTO_FILE_SIZE)
        )
            return false;
        return true;
    }
    
    public function getContent() 
    {
        $output = '';
        $valid = true;
        $photosLimit = 0;
        $maxPhotoFileSize = 0;
        if (Tools::isSubmit('submit' . $this->name)) {
            $photosLimit = (string) Tools::getValue(self::CONFIG_PHOTOS_LIMIT);
            $maxPhotoFileSize = (string) Tools::getValue(self::CONFIG_MAX_PHOTO_FILE_SIZE);
            if (empty($photosLimit)) {
                $output .= $this->displayError($this->l('Enter max allowed photos limit'));
                $valid = false;
            } 
            if (!Validate::isUnsignedInt($photosLimit)) {
                $output .= $this->displayError($this->l('Max allowed photos limit must be a number'));
                $valid = false;
            } 
            if (empty($maxPhotoFileSize)) {
                $output .= $this->displayError($this->l('Enter max photo file size'));
                $valid = false;
            } 
            if (!Validate::isUnsignedInt($maxPhotoFileSize)) {
                $output .= $this->displayError($this->l('Max photo file size must be a number'));
                $valid = false;
            } 
            if($valid){
                Configuration::updateValue(self::CONFIG_PHOTOS_LIMIT, $photosLimit);
                Configuration::updateValue(self::CONFIG_MAX_PHOTO_FILE_SIZE, $maxPhotoFileSize);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output . $this->displayForm();
    }
    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Webstrum Additional Gallery Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Max allowed photos'),
                        'name' => self::CONFIG_PHOTOS_LIMIT,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Max photo file size (kb)'),
                        'name' => self::CONFIG_MAX_PHOTO_FILE_SIZE,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ],
            ],
        ];

        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->generateFieldsValues($helper, self::CONFIG_PHOTOS_LIMIT);
        $this->generateFieldsValues($helper, self::CONFIG_MAX_PHOTO_FILE_SIZE);
        return $helper->generateForm([$form]);
    }
    private function generateFieldsValues(HelperForm &$helper, string $val)
    {
        $helper->fields_value[$val] = Tools::getValue($val, Configuration::get($val));
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        $max_photos_limit = Configuration::get(self::CONFIG_PHOTOS_LIMIT);
        $id_product = $params['product']->id;
        $photos_product = DBHelper::executeS("*", "product_id", $id_product, "", $max_photos_limit);
        $this->context->smarty->assign(array(
             'id_product' => $id_product,
             'additional_photos' => $photos_product
         ));
        if(sizeof($photos_product) > 0)
            return $this->display(__FILE__, self::PRODUCT_GALLERY_TEMPLATE);
    }
    public function hookDisplayBackOfficeHeader()
    {
        $this->putJsCss("back");
    }
    public function hookHeader()
    {
        $this->putJsCss("front");
    }
    public function putJsCss($type)
    {
        $this->context->controller->addJquery();
        $this->context->controller->addJS(array(
            $this->_path.'views/js/common.js',
            $this->_path.'views/js/'.$type.'.js'        
            ));
        $this->context->controller->addCSS(array(
            $this->_path.'views/css/common.css',
            $this->_path.'views/css/'.$type.'.css'
        ));
    }
    public function hookDisplayAdminProductsExtra($params)
    {
         $id_product = $params['id_product'];
         $name_product = Product::getProductName($id_product);
         $this->context->smarty->assign(array(
             'id_product' => $id_product,
             'name_product' => $name_product
         ));
         return $this->display(__FILE__, self::ADD_PRODUCT_GALLERY_TEMPLATE);
    }
}
