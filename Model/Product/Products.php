<?php

namespace DWD\KiyohReviews\Model\Product;


use DWD\KiyohReviews\Helper\Logger;
use Magento\Catalog\Model\ProductRepository;
use \Magento\Framework\Api\Filter;
use \Magento\Framework\Api\Search\FilterGroup;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use \Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Model\ResourceModel\Product\Collection;
use \DWD\KiyohReviews\Helper\Data as HelperData;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;

class Products
{
    // kiyoh bulk api accept max 200 product per request
    const MAX_PRODUCTS_ARRAY = 100;

    protected $SearchFilter;

    protected $filterGroup;

    protected $productData = array();

    protected $SearchCriteriaBuilder;

    protected $ProductRepositoryInterface;

    protected $configurableProductType;

    protected $storeManagerInterface;

    protected $productCollection;

    protected $imageHelper;

    protected $helperData;

    protected $storeManager;

    protected $scopeConfig;

    protected $productRepository;

    protected $productVisibility;

    protected $productStatus;

    public function __construct(
        Logger $helperLogger,
        ProductRepository $productRepository,
        Configurable $configurableProductType,
        ProductRepositoryInterface $ProductRepositoryInterface,
        SearchCriteriaBuilder $SearchCriteriaBuilder,
        ImageHelper $imageHelper,
        StoreManagerInterface $storeManagerInterface,
        Filter $searchFilter,
        FilterGroup $filterGroup,
        Collection $productCollection,
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Visibility $productVisibility,
        Status $productStatus
    )
    {
        $this->storeManager = $storeManager;
        $this->configurableProductType = $configurableProductType;
        $this->ProductRepositoryInterface = $ProductRepositoryInterface;
        $this->SearchCriteriaBuilder = $SearchCriteriaBuilder;
        $this->imageHelper = $imageHelper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->filterGroup = $filterGroup;
        $this->SearchFilter = $searchFilter;
        $this->productCollection = $productCollection;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->productVisibility = $productVisibility;
        $this->productStatus = $productStatus;
        $this->helperLogger = $helperLogger;

    }


    public function getSingleProduct($id)
    {
        $searchCriteria = $this->SearchCriteriaBuilder->addFilter('entity_id', $id, 'eq')->create();
        $products = $this->ProductRepositoryInterface->getList($searchCriteria)->getItems();
        $location_id = $this->helperData->getKiyohLocationId();
        foreach ($this->storeManager->getStores() as $store) {
            $result = $this->createProductFeedArray($products, $store->getId(), $location_id);
            if ($result) {
                array_push($this->productData, $result);
            }
        }
        return $this->productData;
    }

    public function getProductData()
    {
        $location_id = $this->helperData->getKiyohLocationId();
        foreach ($this->storeManager->getStores() as $store) {
            if($store->getId() == 0)
                continue;

            $this->productCollection->setPageSize(self::MAX_PRODUCTS_ARRAY);
            $this->productCollection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])->addStoreFilter($store->getId());
            $this->productCollection->setVisibility($this->productVisibility->getVisibleInSiteIds())->addStoreFilter($store->getId());
            $collection = $this->productCollection->load();
            $nrOfPages = $this->productCollection->getLastPageNumber();
            for ($iCurrentPage = 1; ($iCurrentPage <= $nrOfPages); $iCurrentPage++) {
                $products = $collection->setCurPage($iCurrentPage);
                $result = $this->createProductFeedArray($products, $store->getId(), $location_id);
                if ($result) {
                    array_push($this->productData, $result);
                }
                $collection->clear();
            }
        }

        return $this->productData;
    }


    protected function createProductFeedArray($products, $storeId, $location_id)
    {
        $productsPerMax = array();
        foreach ($products as $product) {
            if (!in_array($storeId, $product->getStoreIds())) {
                continue;
            }

            // only simple and configurable. We not send simple part of configurable
            if (!$this->configurableProductType->getParentIdsByChild($product->getId())) {
                $productData = $this->ProductRepositoryInterface->getById($product->getId(), false, $storeId,true);
                array_push($productsPerMax,
                    array(
                        "location_id" => $location_id,
                        "product_code" => $product->getId(),
                        "product_name" => $productData->getName(),
                        "image_url" => $this->getImageUrl($productData, $storeId),
                        "source_url" => $this->getCleanProductUrl($product, $storeId),
                        "active" => true
                    )
                );
            }
        }
        return $productsPerMax;
    }

    protected function getCleanProductUrl($product, $storeId)
    {
        $product = $this->productRepository->getById($product->getId(), false, $storeId);
        $isSecure = $this->scopeConfig->isSetFlag('web/secure/use_in_frontend', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $productUrl = $product->getUrlModel()->getUrl($product, ['_secure' => $isSecure]);
        $productUrl = $this->replaceStoreUrl($productUrl, $storeId);

        return $productUrl;
    }

    /**
     * @param $product \Magento\Catalog\Api\Data\ProductInterface
     * @return string
     */
    protected function getImageUrl($product, $storeId)
    {
        $imageUrl = $product->getImage();
        if ($imageUrl) {
            $imageUrl = $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $imageUrl;
        } else {
            $imageUrl = $this->imageHelper->getDefaultPlaceholderUrl('image');
        }
        return $this->replaceStoreUrl($imageUrl, $storeId);
    }

    protected function replaceStoreUrl($url, $storeId)
    {
        $isSecure = $this->scopeConfig->isSetFlag('web/secure/use_in_frontend', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        /**
         * There is a Magento bug with getting product URL from adminhtml area:
         * https://github.com/magento/magento2/issues/4247
         * https://github.com/magento/magento2/issues/19196
         *
         * Making a workaround to return proper frontend product URL based on store
         */
        $url = str_replace(
            $this->getBaseUrl(0, $isSecure),
            $this->getBaseUrl($storeId, $isSecure),
            $url
        );

        return $url;
    }


    protected function getBaseUrl($storeId, $isSecure)
    {
        if ($isSecure) {
            return $this->scopeConfig->getValue(
                'web/secure/base_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $storeId
            );
        }

        return $this->scopeConfig->getValue(
            'web/unsecure/base_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
