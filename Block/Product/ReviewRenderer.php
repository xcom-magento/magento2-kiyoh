<?php
/**
 * Review renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DWD\KiyohReviews\Block\Product;

use DWD\KiyohReviews\Model\Get\Reviews;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Review\Model\ReviewSummaryFactory;

class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'Magento_Review::helper/summary.phtml',
        self::SHORT_VIEW => 'Magento_Review::helper/summary_short.phtml',
    ];

    protected $_reviewFactory;

    protected $_reviewModel;

    protected $productMetadata;


    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        Reviews $reviewModel,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = []
    )
    {
        $this->productMetadata = $productMetadata;
        $this->_reviewModel = $reviewModel;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $reviewFactory, $data);
    }

    /**
     * Get review summary html
     *
     * @param Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    )
    {
        if ($this->getMagentoVersion() < 2.3) {
            if (!$product->getRatingSummary()) {
                $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
            }
        } else {
            $reviewFactory = ObjectManager::getInstance()->get(ReviewSummaryFactory::class);
            $reviewFactory->create()->appendSummaryDataToObject(
                $product,
                $this->_storeManager->getStore()->getId()
            );
        }
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = self::DEFAULT_VIEW;
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getMagentoVersion()
    {
        return (float)$this->productMetadata->getVersion();
    }
}
