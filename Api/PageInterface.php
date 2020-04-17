<?php

namespace Jraisanen\Storefront\Api;

interface PageInterface
{
    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    );

    /**
     * Pages
     *
     * @api
     * @return array
     */
    public function pages();

    /**
     * Page
     *
     * @api
     * @param string $key
     * @return array
     */
    public function page($key);
}
