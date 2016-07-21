<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Shopware Google Plugin
 */
class Shopware_Plugins_Frontend_SwagGoogle_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * Returns the version of the plugin as a string
     *
     * @return string
     * @throws Exception
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * Returns the well-formatted name of the plugin
     * as a sting
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Google Services';
    }

    /**
     * Returns the meta information about the plugin
     * as an array.
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'name' => 'SwagGoogle',
            'link' => 'http://www.shopware.de',
            'description' => 'Shopware integration for Google Analytics, Universal Analytics and Google Adwords services'
        );
    }

    /**
     * Install plugin method
     *
     * @return bool
     */
    public function install()
    {
        $this->registerEvents();
        $this->createForm();

        return true;
    }

    /**
     * Register Events
     */
    private function registerEvents()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend',
            'onPostDispatch'
        );
    }

    /**
     * Create the Plugin Settings Form
     */
    public function createForm()
    {
        $form = $this->Form();
        /** @var \Shopware\Models\Config\Form $parent */
        $parent = $this->Forms()->findOneBy(array('name' => 'Interface'));
        $form->setParent($parent);
        $form->setElement('text', 'tracking_code', array(
            'label' => 'Google Analytics-ID',
            'value' => null,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
        $form->setElement('text', 'conversion_code', array(
            'label' => 'Google Conversion-ID',
            'value' => null,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
        $form->setElement('text', 'conversion_label', array(
            'label' => 'Google Conversion-Label',
            'value' => null,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
        $form->setElement('checkbox', 'anonymize_ip', array(
            'label' => 'IP-Adresse anonymisieren',
            'value' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
        $form->setElement('combo', 'trackingLib', array(
            'label' => 'Tracking Bibliothek',
            'value' => 'ga',
            'store' => array(
                array('ga', 'Google Analytics'),
                array('ua', 'Universal Analytics'),
            ),
            'description' => 'Welche Tracking Bibliothek soll benutzt werden? Standardmäßig wird die veraltete Google Analytics verwendet. Der Wechsel zur Universal-Analytics-Bibliothek erfordert, das Sie Ihre Google Analytics Einstellungen aktualisieren. Für mehr Informationen besuchen Sie die offizielle Google-Dokumentation.',
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));

        $form->setElement('checkbox', 'include_header', array(
            'label' => 'Tracking-Code im "head"-Bereich inkludieren (Responsive Theme)',
            'value' => false,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));

        $this->addFormTranslations(
            array(
                'en_GB' => array(
                    'plugin_form' => array(
                        'label' => 'Google Services'
                    ),
                    'trackingLib' => array(
                        'label' => 'Tracking library',
                        'description' => 'Tracking library to use. Defaults to legacy Google Analytics. Switching to Universal Analytics requires that you update you settings in your Google Analytics Admin page. Please check Google\'s official documentation for more info.'
                    ),
                    'tracking_code' => array(
                        'label' => 'Google Analytics ID'
                    ),
                    'conversion_code' => array(
                        'label' => 'Google Conversion ID'
                    ),
                    'anonymize_ip' => array(
                        'label' => 'Anonymous IP address'
                    ),
                    'include_header' => array(
                        'label' => 'Include the tracking code in the "head" section (Responsive theme)'
                    )
                )
            )
        );
    }

    /**
     * Event listener method
     *
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatch(Enlight_Controller_ActionEventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $view = $args->getSubject()->View();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $config = $this->Config();
        if (empty($config->tracking_code) && empty($config->conversion_code)) {
            return;
        }

        $view->addTemplateDir(__DIR__.'/Views/Common');
        $view->addTemplateDir(__DIR__.'/Views/');

        if (!empty($config->conversion_code)) {
            $view->GoogleConversionID = $config->conversion_code;
            $view->GoogleConversionLabel = $config->conversion_label;
            $view->GoogleConversionLanguage = Shopware()->Locale()->getLanguage();
            $view->GoogleIncludeInHead = $config->include_header;
        }
        if (!empty($config->tracking_code)) {
            $view->GoogleTrackingID = $config->tracking_code;
            $view->GoogleAnonymizeIp = $config->anonymize_ip;
            $view->GoogleTrackingLibrary = $config->trackingLib;
            $view->GoogleIncludeInHead = $config->include_header;
        }
    }
}
