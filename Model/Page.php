<?php

namespace Jraisanen\Storefront\Model;

use Magento\Framework\Webapi\Exception;
use Jraisanen\Storefront\Api\PageInterface;

class Page implements PageInterface
{
    private $_pageRepository;
    private $_request;
    private $_searchCriteria;
    private $_storeManager;

    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_pageRepository = $pageRepository;
        $this->_request = $request;
        $this->_searchCriteria = $searchCriteria;
        $this->_storeManager = $storeManager;
    }

    /**
     * Pages
     *
     * @api
     * @throws string
     * @return array
     */
    public function pages() {
        try {
            // Exclude page
            if ($this->_request->getParam('exclude')) {
                $this->_searchCriteria->addFilter('page_id', ['neq' => $this->_request->getParam('exclude')]);
            }

            // Current page
            if ($this->_request->getParam('page')) {
                $this->_searchCriteria->setCurrentPage($this->_request->getParam('page'));
            }

            // Number of items per page
            if ($this->_request->getParam('limit')) {
                $this->_searchCriteria->setPageSize($this->_request->getParam('limit'));
            }

            // Sort by an attribute
            if ($this->_request->getParam('sortBy') && $this->_request->getParam('sortOrder')) {
                $this->_searchCriteria->setSortOrders([
                    strtoupper($this->_request->getParam('sortBy')) => strtoupper($this->_request->getParam('sortOrder'))
                ]);
            }

            $searchCriteria = $this->_searchCriteria->create();
            $pages = $this->_pageRepository->getList($searchCriteria)->getItems();
        } catch (Exception $e) {
            throw $e;
        }

        return $this->_mapPages($pages);
    }

    /**
     * Page
     *
     * @api
     * @throws string
     * @param string $key
     * @return array
     */
    public function page($key) {
        try {
            $searchCriteria = $this->_searchCriteria->addFilter('identifier', $key)->create();
            $pages = $this->_pageRepository->getList($searchCriteria)->getItems();
        } catch (Exception $e) {
            throw $e;
        }

        return $this->_mapPages($pages);
    }

    private function _mapPages($pages) {
        $data = [];

        foreach ($pages as $page) {
            $data[] = [
                'id' => (int)$page->getId(),
                'key' => $page->getIdentifier(),
                'title' => $page->getTitle(),
                'content' => $page->getContent(),
                'createdAt' => $page->getCreationTime(),
                'updatedAt' => $page->getUpdateTime(),
                'meta' => [
                    'title' => $page->getMetaTitle(),
                    'description' => $page->getMetaDescription()
                ]
            ];
        }

        return $data;
    }
}
