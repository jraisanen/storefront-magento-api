<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\ProductInterface;

class Product implements ProductInterface
{
    private $_categoryCollection;
    private $_productCollection;
    private $_request;
    private $_storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_categoryCollection = $categoryCollection;
        $this->_productCollection = $productCollection;
        $this->_request = $request;
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
            if ($this->_request->getParam('exclude')) {
                $products = $products->addAttributeToFilter('entity_id', ['neq' => $this->_request->getParam('exclude')]);
            }

            // Categories
            if ($this->_request->getParam('category')) {
                $products = $products->addCategoriesFilter(['in' => $this->_request->getParam('category')]);
            }

            // Brands
            if ($this->_request->getParam('brands')) {
                $products = $products->addFieldToFilter('manufacturer', ['in' => $this->_request->getParam('brands')]);
            }

            // Current page
            if ($this->_request->getParam('page')) {
                $products = $products->setCurPage($this->_request->getParam('page'));
            }

            // Number of items per page
            if ($this->_request->getParam('limit')) {
                $products = $products->setPageSize($this->_request->getParam('limit'));
            }

            // Sort by an attribute
            if ($this->_request->getParam('sortBy') && $this->_request->getParam('sortOrder')) {
                $products->addAttributeToSort(
                    strtoupper($this->_request->getParam('sortBy')),
                    strtoupper($this->_request->getParam('sortOrder'))
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
