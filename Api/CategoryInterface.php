<?php
namespace Jraisanen\Storefront\Api;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

interface CategoryInterface
{
    public function __construct(
        CategoryCollectionFactory $categoryCollection,
        ProductCollectionFactory $productCollection,
        StoreManagerInterface $storeManager
    );

    /**
     * Categories
     *
     * @api
     * @return array
     */
    public function categories();
}
