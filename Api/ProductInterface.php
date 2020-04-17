<?php
namespace Jraisanen\Storefront\Api;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

interface ProductInterface
{
    public function __construct(
        CategoryCollectionFactory $categoryCollection,
        Http $httpRequest,
        ProductCollectionFactory $productCollection,
        StoreManagerInterface $storeManager
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
