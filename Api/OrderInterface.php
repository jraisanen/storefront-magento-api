<?php
namespace Jraisanen\Storefront\Api;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

interface OrderInterface
{
    public function __construct(
        AuthInterface $auth,
        Http $httpRequest,
        CollectionFactory $orderCollection,
        SearchCriteriaBuilder $searchCriteria,
        StoreManagerInterface $storeManager
    );

    /**
     * Orders
     *
     * @api
     * @return array
     */
    public function orders();

    /**
     * Order
     *
     * @api
     * @param int $id
     * @return array
     */
    public function order($id);
}
