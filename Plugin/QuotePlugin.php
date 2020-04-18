<?php
namespace Jraisanen\Storefront\Plugin;

use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;

class QuotePlugin
{
    protected $_cartItemExtension;
    protected $_productRepository;

    public function __construct(
        CartItemExtensionFactory $cartItemExtension,
        ProductRepositoryInterfaceFactory $productRepository
    ) {
        $this->_cartItemExtension = $cartItemExtension;
        $this->_productRepository = $productRepository;
    }

    public function afterGet($subject, $quote)
    {
        return $this->setAttributeValue($quote);
    }

    public function afterGetActiveForCustomer($subject, $quote)
    {
        return $this->setAttributeValue($quote);
    }

    private function setAttributeValue($quote)
    {
        if ($quote->getItemsCount()) {
            foreach ($quote->getItems() as $item) {
                $extensionAttributes = $item->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->_cartItemExtension->create();
                }
                $productData = $this->_productRepository->create()->get($item->getSku());
                $images = $productData->getMediaGalleryImages();
                $imagesData = [];
                foreach ($images as $image) {
                    $imagesData[] = $image->getFile();
                }
                $extensionAttributes->setKey($productData->getUrlKey());
                $extensionAttributes->setImages($imagesData);

                $item->setExtensionAttributes($extensionAttributes);
            }
        }

        return $quote;
    }
}
