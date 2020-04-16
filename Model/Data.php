<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\DataInterface;

class Data implements DataInterface
{
    private $_attributeRepository;
    private $_categoryCollection;
    private $_categoryResourceModel;
    private $_categorySetup;
    private $_eavConfig;
    private $_eavSetup;
    private $_productCollection;
    private $_productFactory;
    private $_productRepositoryInterface;
    private $_request;
    private $_storeManager;

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
    ) {
        $this->_attributeRepository = $attributeRepository;
        $this->_categoryCollection = $categoryCollection;
        $this->_categoryResourceModel = $categoryResourceModel;
        $this->_categorySetup = $categorySetup;
        $this->_eavConfig = $eavConfig;
        $this->_eavSetup = $eavSetup;
        $this->_productCollection = $productCollection;
        $this->_productFactory = $productFactory;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
    }

    /**
     * Brands
     *
     * @api
     * @throws string
     * @return null
     */
    public function brands() {
        try {
            $brands = $this->_request->getBodyParams();
            $attribute = $this->_eavConfig->getAttribute('catalog_product', 'manufacturer');
            $options = $attribute->getSource()->getAllOptions();
            foreach ($options as $option) {
                $options['delete'][$option['value']] = true;
                $options['value'][$option['value']] = true;
            }
            $this->_eavSetup->addAttributeOption($options);
            $this->_eavSetup->addAttributeOption([
                'values' => $brands,
                'attribute_id' => $attribute->getId(),
            ]);
            return $brands;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Categories
     *
     * @api
     * @throws string
     * @return null
     */
    public function categories() {
        try {
            $categories = $this->_request->getBodyParams();
            $this->deleteCategories();
            $this->addCategories($categories);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Categories images
     *
     * @api
     * @throws string
     * @return null
     */
    public function categoriesImages() {
        try {
            $categoriesImages = [];
            $categories = $this->_categoryCollection->create()
                ->addAttributeToSelect(['url_key'])
                ->setStore($this->_storeManager->getStore());
            $files = glob('/var/www/magento/pub/media/images/people/s/*.*');
            $folderPath = '/var/www/magento/pub/media/catalog/category';
            foreach ($categories as $category) {
                if ($category->getId() > 2) {
                    $imagePath = $files[array_rand($files)];
                    $filename = basename($imagePath);
                    if (!is_dir($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
                    copy($imagePath, $folderPath . '/' . $filename);
                    $category->setImage($filename);
                    $this->_categoryResourceModel->save($category);
                    $categoriesImages[] = [$category->getId() => $filename];
                }
            }
            return $categoriesImages;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function deleteCategories() {
        $categories = $this->_categoryCollection->create()
            ->setStore($this->_storeManager->getStore());
        foreach ($categories as $category) {
            if ($category->getId() > 2) {
                $this->_categoryResourceModel->delete($category);
            }
        }
    }

    private function addCategories($categories, $parentId = 2, $parentPath = '1/2') {
        foreach ($categories as $category) {
            $new_category = $this->_categorySetup->createCategory([
                'data' => [
                    'parent_id'       => $parentId,
                    'name'            => $category['name'],
                    'path'            => $parentPath,
                    'is_active'       => 1,
                    'include_in_menu' => 1,
                ],
            ]);
            $this->_categoryResourceModel->save($new_category);
            if (count($category['children']) > 0) {
                $this->addCategories(
                    $category['children'],
                    $new_category->getEntityId(),
                    $parentPath . '/' . $new_category->getEntityId(),
                );
            }
        }
    }

    /**
     * Products
     *
     * @api
     * @throws string
     * @return null
     */
    public function products() {
        try {
            $products = $this->_request->getBodyParams();
            $this->deleteProducts();
            $this->addProducts($products);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Products brands
     *
     * @api
     * @throws string
     * @return null
     */
    public function productsBrands() {
        try {
            $productsBrands = [];
            $products = $this->_productCollection->create()->setStore($this->_storeManager->getStore());
            $attribute = $this->_attributeRepository->get('catalog_product', 'manufacturer');
            $manufacturers = $attribute->getSource()->getAllOptions(false);
            foreach ($products as $product) {
                $manufacturersKey = array_rand($manufacturers);
                $product->setCustomAttribute('manufacturer', $manufacturers[$manufacturersKey]['value']);
                $product->save();
                $productsBrands[] = [$product->getId() => $manufacturers[$manufacturersKey]['value']];
            }
            return $productsBrands;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function deleteProducts() {
        $products = $this->_productCollection->create()
            ->setStore($this->_storeManager->getStore());
        foreach ($products as $product) {
            $this->_productRepositoryInterface->delete($product);
        }
        system('rm -rf /var/www/magento/pub/media/catalog/product/*');
    }

    private function addProducts($products) {
        $categories = $this->_categoryCollection->create()
            ->addAttributeToSelect(['path'])
            ->addAttributeToFilter('level', 4)
            ->setStore($this->_storeManager->getStore());

        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = array_diff(explode('/', $category['path'] . '/' . $category['entity_id']), [1, 2]);
        }

        $attribute = $this->_attributeRepository->get('catalog_product', 'manufacturer');
        $manufacturers = $attribute->getSource()->getAllOptions(false);

        for ($i = 1; $i <= 100; $i++) {
            try {
                $product = $this->_productFactory->create();
                $name = 'Product ' . $i;
                $slug = str_replace(' ', '-', $name);
                $prices = [39, 49, 59, 69, 79, 89, 99];
                $pricesKey = array_rand($prices);
                $weights = [4, 6, 8, 10, 12, 14, 16];
                $weightsKey = array_rand($weights);
                $categoryIdsKey = array_rand($categoryIds);
                $manufacturersKey = array_rand($manufacturers);
                $product
                    ->setData('name', $name)
                    ->setData('sku', strtoupper($slug))
                    ->setData('url_key', strtolower($slug))
                    ->setData('price', $prices[$pricesKey])
                    ->setData('weight', $weights[$weightsKey])
                    ->setData('description', $name)
                    ->setData('short_description', $name)
                    ->setData('meta_title', $name)
                    ->setData('meta_description', $name)
                    ->setData('category_ids', $categoryIds[$categoryIdsKey])
                    ->setData('type_id', 'simple')
                    ->setData('attribute_set_id', 4)
                    ->setData('website_ids', [$this->_storeManager->getDefaultStoreView()->getWebsiteId()])
                    ->setData('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setData('stock_data', ['is_in_stock' => 1, 'manage_stock' => 0, 'qty' => 1000])
                    ->setData('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

                if (empty($data['visibility'])) {
                    $product->setData('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                }

                $product->setCustomAttribute('manufacturer', $manufacturers[$manufacturersKey]['value']);

                $files = glob('/var/www/magento/pub/media/images/people/s/*.*');
                for ($j = 1; $j <= 4; $j++) {
                    $product->addImageToMediaGallery($files[array_rand($files)], ['image', 'thumbnail'], true, false);
                }

                $this->_productRepositoryInterface->save($product);
            } catch (Exception $e) {
                var_dump($e);
                throw $e;
            }
        }
    }
}
