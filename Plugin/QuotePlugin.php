<?php
namespace Jraisanen\Storefront\Plugin;

use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;

class QuotePlugin
{
    protected $cartItemExtension;
    protected $productRepository;

    public function __construct(
        CartItemExtensionFactory $cartItemExtension,
        ProductRepositoryInterfaceFactory $productRepository
    ) {
        $this->cartItemExtension = $cartItemExtension;
        $this->productRepository = $productRepository;
    }

    public function afterGet($subject, $quote) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    public function afterGetActiveForCustomer($subject, $quote) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    private function setAttributeValue($quote) {
        if ($quote->getItemsCount()) {
            foreach ($quote->getItems() as $item) {
                $extensionAttributes = $item->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->cartItemExtension->create();
                }
                $productData = $this->productRepository->create()->get($item->getSku());
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
