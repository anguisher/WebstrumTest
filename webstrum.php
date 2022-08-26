<?php

if (!defined('_PS_VERSION_'))
    exit();

class Webstrum extends Module
{
    private $_html = '';
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
            && $this->checkDbTable()
            && $this->registerHook('displayProductAdditionalInfo')
            && Configuration::updateValue('webstrum', 'wlsdMpnDBn8');
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('webstrum'))
            return false;
        return true;
    }
    

    private function checkDbTable(){
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'webstrum_gallery_photos` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `product_id` int(10) NOT NULL,
        `photo_url` varchar(128) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $result = Db::getInstance()->execute($sql);
        return $result;
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $configValue = (string) Tools::getValue('WEBSTRUM_MAX_PHOTOS_LIMIT');
            if (empty($configValue) || !Validate::isGenericName($configValue)) {
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('WEBSTRUM_MAX_PHOTOS_LIMIT', $configValue);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output . $this->displayForm();
    }
    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Max Allowed Photos'),
                        'name' => 'WEBSTRUM_MAX_PHOTOS_LIMIT',
                        'placeholder' => '0',
                        'size' => 2,
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

        $helper->fields_value['WEBSTRUM_MAX_PHOTOS_LIMIT'] = Tools::getValue('WEBSTRUM_MAX_PHOTOS_LIMIT', Configuration::get('WEBSTRUM_MAX_PHOTOS_LIMIT'));
        return $helper->generateForm([$form]);
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        $max_photos_limit = Configuration::get('WEBSTRUM_MAX_PHOTOS_LIMIT');
        $id_product = $params['product']->id;
        $query = "SELECT `photo_url` FROM "._DB_PREFIX_."webstrum_gallery_photos WHERE `product_id`='".$id_product."'"." LIMIT ".$max_photos_limit;
        $result = Db::getInstance()->executeS($query);
        $this->context->smarty->assign(array(
             'id_product' => $id_product,
             'additional_photos' => $result
         ));
        if(sizeof($result) > 0)
            return $this->display(__FILE__, '/views/templates/hook/productGallery.tpl');
    }
    public function hookDisplayBackOfficeHeader(){
        $this->context->controller->addJquery();
        $this->context->controller->addJS(array(
            $this->_path.'views/js/main.js'        
            ));
        $this->context->controller->addCSS(array(
            $this->_path.'views/css/main.css'
        ));
    }
    public function hookHeader(){
        $this->context->controller->addJquery();
        $this->context->controller->addJS(array(
            $this->_path.'views/js/main.js'        
            ));
        $this->context->controller->addCSS(array(
            $this->_path.'views/css/main.css'
        ));
    }
    public function hookDisplayAdminProductsExtra($params)
    {
         $id_product = $params['id_product'];
         $name = Product::getProductName($id_product);
         $this->context->smarty->assign(array(
             'id_product' => $id_product,
             'name_product' => $name
         ));
         return $this->display(__FILE__, '/views/templates/hook/additionalProductGallery.tpl');
    }
}
