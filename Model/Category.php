<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\CategoryInterface;

class Category implements CategoryInterface
{
    private $_categoryCollection;
    private $_productCollection;
    private $_storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_categoryCollection = $categoryCollection;
        $this->_productCollection = $productCollection;
        $this->_storeManager = $storeManager;
    }

    /**
     * Categories
     *
     * @api
     * @throws string
     * @return array
     */
    public function categories() {
        $data = [];

        try {
            $categories = $this->_categoryCollection->create()
                ->addAttributeToSelect(['url_key', 'name', 'image'])
                ->setStore($this->_storeManager->getStore());

            foreach ($categories as $category) {
                $productsCount = $this->_productCollection->create()
                    ->addCategoriesFilter(['eq' => $category->getId()])
                    ->setStore($this->_storeManager->getStore())
                    ->count();

                $data[] = [
                    'id' => (int)$category->getId(),
                    'key' => $category->getUrlKey() ? $category->getUrlKey() : '',
                    'name' => $category->getName() ? $category->getName() : '',
                    'parent' => (int)$category->getParentId(),
                    'image' => $category->getImage(),
                    'products' => $productsCount,
                ];
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $data;
    }
}
