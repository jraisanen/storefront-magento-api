<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\BrandInterface;

class Brand implements BrandInterface
{
    private $_eavConfig;
    private $_productCollection;
    private $_request;
    private $_storeManager;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_productCollection = $productCollection;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
    }

    /**
     * Brands
     *
     * @api
     * @throws string
     * @return string[]
     */
    public function brands() {
        $data = [];

        try {
            $attributeDetails = $this->_eavConfig->getAttribute('catalog_product', 'manufacturer');
            $options = $attributeDetails->getSource()->getAllOptions();

            foreach ($options as $option) {
                $products = $this->_productCollection->create()
                    ->addAttributeToFilter('manufacturer', $option['value'])
                    ->setStore($this->_storeManager->getStore());

                if ($this->_request->getParam('category')) {
                    $products = $products->addCategoriesFilter(['in' => $this->_request->getParam('category')]);
                }

                if ((int)$option['value'] > 0) {
                    $data[] = [
                        'id' => (int)$option['value'],
                        'name' => $option['label'],
                        'type' => 'brand',
                        'products' => $products->count(),
                    ];
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $data;
    }
}
