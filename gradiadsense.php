<?php
/**
* @author arojast95@gmail.com
*/
Class GradiAdsense extends Module {
    public function __construct()
    {
        $this->name = 'gradiadsense';
        $this->version = '1.0.0';
        $this->author = 'arojast95@gmail.com';
        $this->displayName = $this->l('Gradi Adsense');
        $this->description = $this->l('Banner publicidad');
        $this->controllers = array('default');
        $this->bootstrap = 1;
        parent::__construct();
    }
    
    public function install()
    {
        if( !parent::install() || !$this->registerHook('displayHome'))
            return false;

        Configuration::updateValue("GRADI_ADSENSE_IMAGEN", "default.png");
        return true;
    }
    
    public function uninstall()
    {
        if( !parent::uninstall() || !$this->unregisterHook('displayHome'))
            return false;

        Configuration::deleteByName("GRADI_ADSENSE_TEXTO");
        Configuration::deleteByName("GRADI_ADSENSE_DESCRIPCION");
        Configuration::deleteByName("GRADI_ADSENSE_CTA");
        Configuration::deleteByName("GRADI_ADSENSE_CTA_URL");
        Configuration::deleteByName("GRADI_ADSENSE_CHECK_ACTIVO");
        Configuration::deleteByName("GRADI_ADSENSE_IMAGEN");

        return true;
    }
    
    public function getContent()
    {
        return $this->postProcess() . $this->getForm();
    }
    
    public function getForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->getLanguages();
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->title = $this->displayName;

        $helper->submit_action = 'gradiadsense_form';
        $helper->fields_value['texto'] = Configuration::get('GRADI_ADSENSE_TEXTO');
        $helper->fields_value['descripcion'] = Configuration::get('GRADI_ADSENSE_DESCRIPCION');
        $helper->fields_value['cta'] = Configuration::get('GRADI_ADSENSE_CTA');
        $helper->fields_value['cta_url'] = Configuration::get('GRADI_ADSENSE_CTA_URL');
        
        if(Configuration::get('GRADI_ADSENSE_CHECK_ACTIVO') == 1)
            $helper->fields_value['check_activo_'] = true;    
        
        $image = '';
        $background_image = Configuration::get('GRADI_ADSENSE_IMAGEN');
        if ($background_image) {
            $image_url = $background_image ? '../modules/gradiadsense/views/img/' . $background_image . '?r='.rand(1000,10000) : '';
            $image = '<div class="col-lg-6"><img src="' . $image_url . '" class="img-thumbnail" width="400"></div>';
        }
        
        $this->form[0] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->displayName
                 ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->l('Imagen publicitaria'),
                        'name' => 'imagen',
                        'display_image' => true,
                        'image' => $image,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Texto publicidad'),
                        'name' => 'texto',
                        'lang' => false,
                        'required' => true,
                        'v'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Descripción publicidad'),
                        'name' => 'descripcion',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Cta'),
                        'name' => 'cta',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Url Cta'),
                        'name' => 'cta_url',
                        'lang' => false,
                        'required' => true,
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Activo'),
                        'name' => 'check_activo',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'activo',
                                    'name' => 'activo',
                                    'val' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Guardar')
                 )
             )
         );
        
        return $helper->generateForm($this->form);
         
    }
    
    public function postProcess()
    {
        if(Tools::isSubmit('gradiadsense_form')) {
            //imagen
            $archivo = $_FILES['imagen']['name'];
            if(isset($archivo) && $archivo != ""){
                $tipo = $_FILES['imagen']['type'];
                $tamano = $_FILES['imagen']['size'];
                $temp = $_FILES['imagen']['tmp_name'];
                unlink(_PS_MODULE_DIR_.'gradiadsense/img/default.png');
                move_uploaded_file($temp, _PS_MODULE_DIR_.'gradiadsense/views/img/default.png');
            }
            
            $texto = Tools::getValue('texto');
            $descripcion = Tools::getValue('descripcion');
            $cta = Tools::getValue('cta');
            $cta_url = Tools::getValue('cta_url');
            $check_activo = (Tools::getValue('check_activo_') == 1) ? 1:0;
            
            Configuration::updateValue('GRADI_ADSENSE_TEXTO', $texto);
            Configuration::updateValue('GRADI_ADSENSE_DESCRIPCION', $descripcion);
            Configuration::updateValue('GRADI_ADSENSE_CTA', $cta);
            Configuration::updateValue('GRADI_ADSENSE_CTA_URL', $cta_url);
            Configuration::updateValue('GRADI_ADSENSE_CHECK_ACTIVO', $check_activo);
            
            return $this->displayConfirmation($this->l('Se han modificado los datos correctamente'));
        }
    }
    
    public function hookDisplayHome()
    {
        $this->context->controller->registerStylesheet(
            'module-mymodule-style',
            'modules/'.$this->name.'/views/css/styles.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
        
        $imagen = Configuration::get('GRADI_ADSENSE_IMAGEN');
        $texto = Configuration::get('GRADI_ADSENSE_TEXTO');
        $descripcion = Configuration::get('GRADI_ADSENSE_DESCRIPCION');
        $cta = Configuration::get('GRADI_ADSENSE_CTA');
        $cta_url = Configuration::get('GRADI_ADSENSE_CTA_URL');
        $check_activo = Configuration::get('GRADI_ADSENSE_CHECK_ACTIVO');

        $this->context->smarty->assign(array(
            'imagen' => $imagen,
            'texto' => $texto,
            'descripcion' => $descripcion,
            'cta' => $cta,
            'cta_url' => $cta_url,
            'check_activo' => $check_activo,
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/gradiadsense.tpl');
    }
 
}