<?php

namespace Jraisanen\Storefront\Api;

interface ConfigInterface
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Configs
     *
     * @api
     * @return string[]
     */
    public function configs();
}
