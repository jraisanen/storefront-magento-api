<?php
namespace Jraisanen\Storefront\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Jraisanen\Storefront\Api\AuthInterface;
use Jraisanen\Storefront\Api\OrderInterface;

class Order implements OrderInterface
{
    private $_auth;
    private $_httpRequest;
    private $_orderCollection;
    private $_searchCriteria;
    private $_storeManager;

    public function __construct(
        AuthInterface $auth,
        Http $httpRequest,
        CollectionFactory $orderCollection,
        SearchCriteriaBuilder $searchCriteria,
        StoreManagerInterface $storeManager
    ) {
        $this->_auth = $auth;
        $this->_httpRequest = $httpRequest;
        $this->_orderCollection = $orderCollection;
        $this->_searchCriteria = $searchCriteria;
        $this->_storeManager = $storeManager;
    }

    /**
     * Orders
     *
     * @api
     * @throws string
     * @return array
     */
    public function orders()
    {
        $orders = $this->_orderCollection->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('customer_id', $this->_auth->getCustomerId());

        // Current page
        if ($this->_httpRequest->getParam('page')) {
            $orders = $orders->setCurPage($this->_httpRequest->getParam('page'));
        }

        // Number of items per page
        if ($this->_httpRequest->getParam('limit')) {
            $orders = $orders->setPageSize($this->_httpRequest->getParam('limit'));
        }

        // Sort by an attribute
        if ($this->_httpRequest->getParam('sortBy') && $this->_httpRequest->getParam('sortOrder')) {
            $orders->addAttributeToSort(
                strtoupper($this->_httpRequest->getParam('sortBy')),
                strtoupper($this->_httpRequest->getParam('sortOrder'))
            );
        }

        return $this->_mapOrders($orders);
    }

    /**
     * Order
     *
     * @api
     * @throws string
     * @param int $id
     * @return array
     */
    public function order($id)
    {
        $orders = $this->_orderCollection->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('customer_id', $this->_auth->getCustomerId())
            ->addAttributeToFilter('entity_id', $id);

        return $this->_mapOrders($orders);
    }

    private function _mapOrders($orders)
    {
        $data = [];

        foreach ($orders as $order) {
            $data[] = [
                'id' => (int)$order->getId(),
                'state' => $order->getState(),
                'status' => $order->getStatus(),
                'couponCode' => $order->getCouponCode(),
                'shippingDescription' => $order->getShippingDescription(),
                'storeId' => $order->getStoreId(),
                'discountAmount' => $order->getDiscountAmount(),
                'grandTotal' => $order->getGrandTotal(),
                'shippingAmount' => $order->getShippingAmount(),
                'subtotal' => $order->getSubtotal(),
                'taxAmount' => $order->getTaxAmount(),
                'totalPaid' => $order->getTotalPaid(),
                'totalQtyOrdered' => $order->getTotalQtyOrdered(),
                'billingAddressId' => $order->getBillingAddressId(),
                'quoteId' => $order->getQuoteId(),
                'shippingAddressId' => $order->getShippingAddressId(),
                'orderCurrencyCode' => $order->getOrderCurrencyCode(),
                'shippingMethod' => $order->getShippingMethod(),
                'totalItemCount' => $order->getTotalItemCount(),
                'createdAt' => $order->getCreatedAt(),
                'updatedAt' => $order->getUpdatedAt()
            ];
        }

        return $data;
    }
}
