<?php
namespace Jraisanen\Storefront\Model;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\BrandInterface;

class Brand implements BrandInterface
{
    private $_eavConfig;
    private $_httpRequest;
    private $_productCollection;
    private $_storeManager;

    public function __construct(
        Config $eavConfig,
        Http $httpRequest,
        CollectionFactory $productCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_httpRequest = $httpRequest;
        $this->_productCollection = $productCollection;
        $this->_storeManager = $storeManager;
    }

    /**
     * Brands
     *
     * @api
     * @throws string
     * @return array
     */
    public function brands() {
        $data = [];

        try {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', 'manufacturer');

            foreach ($attribute->getSource()->getAllOptions() as $option) {
                if ((int)$option['value'] > 0) {
                    $products = $this->_productCollection->create()
                        ->addAttributeToFilter('manufacturer', $option['value'])
                        ->setStore($this->_storeManager->getStore());

                    if ($this->_httpRequest->getParam('category')) {
                        $filters = ['in' => $this->_httpRequest->getParam('category')];
                        $products = $products->addCategoriesFilter($filters);
                    }

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
