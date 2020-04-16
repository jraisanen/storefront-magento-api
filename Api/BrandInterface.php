<?php

namespace Jraisanen\Storefront\Api;

interface BrandInterface
{
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Brands
     *
     * @api
     * @return string[]
     */
    public function brands();
}
