<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\ConfigInterface;

class Config implements ConfigInterface
{
    private $_scopeConfig;
    private $_storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Configs
     *
     * @api
     * @throws string
     * @return string[]
     */
    public function configs() {
        $data = [];

        try {
            foreach ($this->_storeManager->getStores() as $store) {
                $data[] = [
                    'id' => (int)$store->getId(),
                    'code' => $store->getCode(),
                    'websiteId' => (int)$store->getWebsiteId(),
                    'name' => $store->getName(),
                    'locale' => $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()),
                    'currencyCode' => $this->_scopeConfig->getValue('currency/options/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()),
                    'timezone' => $this->_scopeConfig->getValue('general/locale/timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()),
                    'weightUnit' => $this->_scopeConfig->getValue('general/locale/weight_unit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()),
                    'baseUrl' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true),
                    'baseMediaUrl' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, true),
                ];
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $data;
    }
}
