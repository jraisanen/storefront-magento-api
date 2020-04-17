<?php
namespace Jraisanen\Storefront\Api;

use Magento\Framework\App\Request\Http;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;

interface PageInterface
{
    public function __construct(
        Http $httpRequest,
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteria,
        StoreManagerInterface $storeManager
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
