<?php
namespace Jraisanen\Storefront\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

interface ConfigInterface
{
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    );

    /**
     * Configs
     *
     * @api
     * @return array
     */
    public function configs();
}
