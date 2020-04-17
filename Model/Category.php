<?php
namespace Jraisanen\Storefront\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\CategoryInterface;

class Category implements CategoryInterface
{
    private $_categoryCollection;
    private $_productCollection;
    private $_storeManager;

    public function __construct(
        CategoryCollectionFactory $categoryCollection,
        ProductCollectionFactory $productCollection,
        StoreManagerInterface $storeManager
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
