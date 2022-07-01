<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SwagGoogle;

use Enlight_Controller_Request_Request;
use Enlight_View_Default;
use Shopware\Bundle\CookieBundle\CookieCollection;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Configuration\CachedReader;
use Shopware\Components\Plugin\Configuration\ReaderInterface;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Shop\Shop;
use Zend_Locale as Locale;

class SwagGoogle extends Plugin
{
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function uninstall(UninstallContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Widgets' => 'onPostDispatch',
            'CookieCollector_Collect_Cookies' => 'addGoogleAnalyticsCookie',
        ];
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args): void
    {
        $controller = $args->get('subject');

        /** @var Enlight_Controller_Request_Request $request */
        $request = $controller->Request();

        /** @var Enlight_View_Default $view */
        $view = $controller->View();

        $view->addTemplateDir($this->getPath() . '/Resources/views');

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $config = $this->getConfig();
        if (!empty($config['conversion_code'])) {
            $this->handleConversionCode($view, $config);
        }

        if (!empty($config['tracking_code'])) {
            $this->handleTrackingCode($view, $config);
        }
    }

    /**
     * @return CookieCollection
     */
    public function addGoogleAnalyticsCookie()
    {
        $config = $this->getConfig();

        $collection = new CookieCollection();
        $trackingLib = isset($config['trackingLib']) ? $config['trackingLib'] : 'ua';
        $collection->add($this->getCookieStruct($trackingLib));

        return $collection;
    }

    /**
     * @param string $usedLibraryKey
     *
     * @return CookieStruct
     */
    private function getCookieStruct($usedLibraryKey)
    {
        if ($usedLibraryKey === 'ga') {
            return new CookieStruct(
                '__utm',
                '/^__utm.*$/',
                'Google Analytics',
                CookieGroupStruct::STATISTICS
            );
        }

        return new CookieStruct(
            '_ga',
            '/(^_g(a|at|id)$)|AMP_TOKEN|^_gac_.*$/',
            'Google Analytics',
            CookieGroupStruct::STATISTICS
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfig(): array
    {
        $shop = $this->container->get('shop');
        if (!$shop instanceof Shop) {
            throw new \RuntimeException('Shop not found');
        }

        $config = $this->container->get(CachedReader::class);
        if (!$config instanceof ReaderInterface) {
            throw new \RuntimeException('CachedConfigReader not found');
        }

        return $config->getByPluginName($this->getName(), $shop->getId());
    }

    /**
     * @param array<string, mixed> $config
     */
    private function handleConversionCode(Enlight_View_Default $view, array $config): void
    {
        $locale = $this->container->get('locale');
        if (!$locale instanceof Locale) {
            throw new \RuntimeException('Locale not found');
        }

        $view->assign('GoogleConversionID', trim($config['conversion_code']));
        $view->assign('GoogleConversionLabel', trim($config['conversion_label']));
        $view->assign('GoogleConversionLanguage', $locale->getLanguage());
        $view->assign('GoogleIncludeInHead', $config['include_header']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function handleTrackingCode(Enlight_View_Default $view, array $config): void
    {
        $view->assign('GoogleTrackingID', trim($config['tracking_code']));
        $view->assign('GoogleAnonymizeIp', $config['anonymize_ip']);
        $view->assign('GoogleOptOutCookie', $config['include_opt_out_cookie']);
        $view->assign('GoogleTrackingLibrary', $config['trackingLib']);
        $view->assign('GoogleIncludeInHead', $config['include_header']);
    }
}
