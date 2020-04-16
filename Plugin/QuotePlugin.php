<?php

namespace Jraisanen\Storefront\Plugin;

use Magento\Quote\Api\Data\CartInterface;

class QuotePlugin {

    /**
     * @var \Magento\Quote\Api\Data\CartItemExtensionFactory
     */
    protected $cartItemExtension;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtension
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Quote\Api\Data\CartItemExtensionFactory $cartItemExtension,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository) {
        $this->cartItemExtension = $cartItemExtension;
        $this->productRepository = $productRepository;
    }

    /**
     * Add attribute values
     *
     * @param   \Magento\Quote\Api\CartRepositoryInterface $subject,
     * @param   $quote
     * @return  $quoteData
     */
    public function afterGet(
        \Magento\Quote\Api\CartRepositoryInterface $subject, $quote
    ) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    /**
     * Add attribute values
     *
     * @param   \Magento\Quote\Api\CartRepositoryInterface $subject,
     * @param   $quote
     * @return  $quoteData
     */
    public function afterGetActiveForCustomer(
        \Magento\Quote\Api\CartRepositoryInterface $subject, $quote
    ) {
        $quoteData = $this->setAttributeValue($quote);
        return $quoteData;
    }

    /**
     * set value of attributes
     *
     * @param   $product,
     * @return  $extensionAttributes
     */
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
