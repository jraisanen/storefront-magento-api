<?php

namespace Jraisanen\Storefront\Api;

interface ProductInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Products
     *
     * @api
     * @return array
     */
    public function products();

    /**
     * Product
     *
     * @api
     * @param string $key
     * @return array
     */
    public function product($key);
}
