<?php

namespace Jraisanen\Storefront\Api;

interface DataInterface
{
    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResourceModel,
        \Magento\Catalog\Setup\CategorySetup $categorySetup,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Brands
     *
     * @api
     * @return array
     */
    public function brands();

    /**
     * Categories
     *
     * @api
     * @return array
     */
    public function categories();

    /**
     * Categories images
     *
     * @api
     * @return array
     */
    public function categoriesImages();

    /**
     * Products
     *
     * @api
     * @return array
     */
    public function products();

    /**
     * Products brands
     *
     * @api
     * @return array
     */
    public function productsBrands();
}
