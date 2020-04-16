<?php

namespace Jraisanen\Storefront\Api;

interface CategoryInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Categories
     *
     * @api
     * @return array
     */
    public function categories();
}
