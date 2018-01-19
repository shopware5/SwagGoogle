<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;

/**
 * Shopware Google Plugin
 */
class Shopware_Plugins_Frontend_SwagGoogle_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * Returns the version of the plugin as a string
     *
     * @return string
     * @throws RunTimeException
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new RunTimeException('The plugin has an invalid version file.');
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
        return [
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'name' => 'SwagGoogle',
            'link' => 'http://www.shopware.de',
            'description' => 'Shopware integration for Google Analytics, Universal Analytics and Google Adwords services'
        ];
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
        /** @var Form $parent */
        $parent = $this->Forms()->findOneBy(['name' => 'Interface']);
        $form->setParent($parent);
        $form->setElement('text', 'tracking_code', [
            'label' => 'Google Analytics-ID',
            'value' => null,
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('text', 'conversion_code', [
            'label' => 'Google Conversion-ID',
            'value' => null,
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('text', 'conversion_label', [
            'label' => 'Google Conversion-Label',
            'value' => null,
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('text', 'optimize_code', [
            'label' => 'Google Optimize-ID',
            'value' => null,
            'description' => 'Um die Funktion von Google Optimize nutzen zu können, muss die Google Universal Analytics Bibliothek genutzt werden.',
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('checkbox', 'anonymize_ip', [
            'label' => 'IP-Adresse anonymisieren',
            'value' => true,
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('combo', 'trackingLib', [
            'label' => 'Tracking Bibliothek',
            'value' => 'ga',
            'store' => [
                ['ga', 'Google Analytics'],
                ['ua', 'Universal Analytics'],
            ],
            'description' => 'Welche Tracking Bibliothek soll benutzt werden? Standardmäßig wird die veraltete Google Analytics verwendet. Der Wechsel zur Universal-Analytics-Bibliothek erfordert, das Sie Ihre Google Analytics Einstellungen aktualisieren. Für mehr Informationen besuchen Sie die offizielle Google-Dokumentation.',
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('checkbox', 'include_opt_out_cookie', [
            'label' => 'Opt-Out Cookie ermöglichen',
            'description' => 'Opt-Out Cookie setzen, so dass der Datenfluss nach Google unterbochen werden kann: https://developers.google.com/analytics/devguides/collection/gajs/',
            'value' => false,
            'scope' => Element::SCOPE_SHOP
        ]);
        $form->setElement('checkbox', 'include_header', [
            'label' => 'Tracking-Code im "head"-Bereich inkludieren (Responsive Theme)',
            'value' => false,
            'scope' => Element::SCOPE_SHOP
        ]);
        $this->addFormTranslations(
            [
                'en_GB' => [
                    'plugin_form' => [
                        'label' => 'Google Services'
                    ],
                    'trackingLib' => [
                        'label' => 'Tracking library',
                        'description' => 'Tracking library to use. Defaults to legacy Google Analytics. Switching to Universal Analytics requires that you update you settings in your Google Analytics Admin page. Please check Google\'s official documentation for more info.'
                    ],
                    'tracking_code' => [
                        'label' => 'Google Analytics ID'
                    ],
                    'conversion_code' => [
                        'label' => 'Google Conversion ID'
                    ],
                    'optimize_code' => [
                        'label' => 'Google Optimize-ID',
                        'description' => 'Google Universal Analytics libary must be selected to use Google Optimize'
                    ],
                    'anonymize_ip' => [
                        'label' => 'Anonymous IP address'
                    ],
                    'include_opt_out_cookie' => [
                        'label' => 'Allow opt-out cookie',
                        'description' => 'The tracking snippet includes a window property disables the tracking snippet from sending data to Google Analytics: https://developers.google.com/analytics/devguides/collection/gajs/'
                    ],
                    'include_header' => [
                        'label' => 'Include the tracking code in the "head" section (Responsive theme)'
                    ]
                ]
            ]
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

        $view->addTemplateDir(__DIR__ . '/Views/Common');
        $view->addTemplateDir(__DIR__ . '/Views/');

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $config = $this->Config();
        $trackingCode = $config->get('tracking_code');
        $conversionCode = $config->get('conversion_code');
        if (empty($trackingCode) && empty($conversionCode)) {
            return;
        }

        if (!empty($config->conversion_code)) {
            $view->assign('GoogleConversionID', $config->get('conversion_code'));
            $view->assign('GoogleConversionLabel', $config->get('conversion_label'));
            $view->assign('GoogleConversionLanguage', $this->get('locale')->getLanguage());
            $view->assign('GoogleIncludeInHead', $config->get('include_header'));
        }
        if (!empty($config->tracking_code)) {
            $view->assign('GoogleTrackingID', $config->get('tracking_code'));
            $view->assign('GoogleOptimizeID', $config->get('optimize_code'));
            $view->assign('GoogleAnonymizeIp', $config->get('anonymize_ip'));
            $view->assign('GoogleOptOutCookie', $config->get('include_opt_out_cookie'));
            $view->assign('GoogleTrackingLibrary', $config->get('trackingLib'));
            $view->assign('GoogleIncludeInHead', $config->get('include_header'));
        }
    }
}
