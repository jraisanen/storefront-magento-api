<?php
namespace Jraisanen\Storefront\Api;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

interface BrandInterface
{
    public function __construct(
        Config $eavConfig,
        Http $httpRequest,
        CollectionFactory $productCollection,
        StoreManagerInterface $storeManager
    );

    /**
     * Brands
     *
     * @api
     * @return array
     */
    public function brands();
}
