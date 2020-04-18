<?php
namespace Jraisanen\Storefront\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Jraisanen\Storefront\Api\ConfigInterface;

class Config implements ConfigInterface
{
    private $_scopeConfig;
    private $_storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Configs
     *
     * @api
     * @throws string
     * @return array
     */
    public function configs()
    {
        $data = [];

        foreach ($this->_storeManager->getStores() as $store) {
            $data[] = [
                'id' => (int)$store->getId(),
                'code' => $store->getCode(),
                'websiteId' => (int)$store->getWebsiteId(),
                'name' => $store->getName(),
                'locale' => $this->_scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $store->getId()),
                'currencyCode' => $this->_scopeConfig->getValue('currency/options/default', ScopeInterface::SCOPE_STORE, $store->getId()),
                'timezone' => $this->_scopeConfig->getValue('general/locale/timezone', ScopeInterface::SCOPE_STORE, $store->getId()),
                'weightUnit' => $this->_scopeConfig->getValue('general/locale/weight_unit', ScopeInterface::SCOPE_STORE, $store->getId()),
                'baseUrl' => $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, true),
                'baseMediaUrl' => $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, true),
            ];
        }

        return $data;
    }
}
