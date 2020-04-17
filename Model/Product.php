<?php
namespace Jraisanen\Storefront\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\ProductInterface;

class Product implements ProductInterface
{
    private $_categoryCollection;
    private $_httpRequest;
    private $_productCollection;
    private $_storeManager;

    public function __construct(
        CategoryCollectionFactory $categoryCollection,
        Http $httpRequest,
        ProductCollectionFactory $productCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->_categoryCollection = $categoryCollection;
        $this->_httpRequest = $httpRequest;
        $this->_productCollection = $productCollection;
        $this->_storeManager = $storeManager;
    }

    /**
     * Products
     *
     * @api
     * @throws string
     * @return array
     */
    public function products() {
        try {
            $products = $this->_productCollection->create()
                ->addAttributeToSelect(['url_key', 'sku', 'name', 'price', 'images'])
                ->setStore($this->_storeManager->getStore());

            // Exclude product
            if ($this->_httpRequest->getParam('exclude')) {
                $products = $products->addAttributeToFilter('entity_id', ['neq' => $this->_httpRequest->getParam('exclude')]);
            }

            // Categories
            if ($this->_httpRequest->getParam('category')) {
                $products = $products->addCategoriesFilter(['in' => $this->_httpRequest->getParam('category')]);
            }

            // Brands
            if ($this->_httpRequest->getParam('brands')) {
                $products = $products->addFieldToFilter('manufacturer', ['in' => $this->_httpRequest->getParam('brands')]);
            }

            // Current page
            if ($this->_httpRequest->getParam('page')) {
                $products = $products->setCurPage($this->_httpRequest->getParam('page'));
            }

            // Number of items per page
            if ($this->_httpRequest->getParam('limit')) {
                $products = $products->setPageSize($this->_httpRequest->getParam('limit'));
            }

            // Sort by an attribute
            if ($this->_httpRequest->getParam('sortBy') && $this->_httpRequest->getParam('sortOrder')) {
                $products->addAttributeToSort(
                    strtoupper($this->_httpRequest->getParam('sortBy')),
                    strtoupper($this->_httpRequest->getParam('sortOrder'))
                );
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $this->_mapProducts($products);
    }

    /**
     * Product
     *
     * @api
     * @throws string
     * @param string $key
     * @return array
     */
    public function product($key) {
        try {
            $products = $this->_productCollection->create()
                ->addAttributeToSelect(['url_key', 'sku', 'name', 'description', 'price', 'images'])
                ->addAttributeToFilter('url_key', $key)
                ->setStore($this->_storeManager->getStore());
        } catch (Exception $e) {
            throw $e;
        }

        return $this->_mapProducts($products);
    }

    private function _mapProducts($products) {
        $data = [];

        foreach ($products as $product) {
            $product->load('media_gallery');
            $images = $product->getMediaGalleryImages();
            $imagesData = [];

            foreach ($images as $image) {
                $imagesData[] = $image->getFile();
            }

            $categoriesData = [];
            $categories = $this->_categoryCollection->create()
                ->addAttributeToSelect(['url_key', 'name', 'level'])
                ->addAttributeToFilter('entity_id', $product->getCategoryIds())
                ->setStore($this->_storeManager->getStore());

            foreach ($categories as $category) {
                $categoriesData[] = [
                    'id' => $category['entity_id'],
                    'url_key' => $category['url_key'],
                    'name' => $category['name'],
                    'level' => $category['level'],
                ];
            }

            $data[] = [
                'id' => (int)$product->getId(),
                'key' => $product->getUrlKey() ? $product->getUrlKey() : '',
                'sku' => $product->getSku() ? $product->getSku() : '',
                'name' => $product->getName() ? $product->getName() : '',
                'description' => $product->getDescription() ? $product->getDescription() : '',
                'price' => $product->getPrice() ? (int)$product->getPrice() : 0,
                'images' => $imagesData,
                'categories' => $categoriesData,
                'brand' => $product->getAttributeText('manufacturer'),
            ];
        }

        return $data;
    }
}
