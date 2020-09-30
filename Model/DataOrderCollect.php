<?php

namespace DWD\KiyohReviews\Model;

use DWD\KiyohReviews\Helper\Data as HelperData;
use Magento\Sales\Api\Data\OrderInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;

class DataOrderCollect
{
    protected $orderRepository;

    protected $helperData;

    public function __construct(
        HelperData $helperData,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }

    /**
     * Get api class
     *
     * @param OrderInterface $order
     * @return array
     * @internal param Store $store
     */
    public function createInviteDataProduct($order)
    {
        $delay = $this->helperData->getKiyohDelay();
        $location_id = $this->helperData->getKiyohLocationId();
        $emailLang = $this->helperData->getKiyohEmailLang($order->getStoreId());
        $items = $order->getAllVisibleItems();

        $productIds = array();
        foreach ($items as $key => $item) {
            if ($key < $this->helperData->getKiyohInputMaxInviteProducts())
                $productIds[] = $item->getProductId();
        }

        $postData = array(
            "location_id" => $location_id,
            "invite_email" => $order->getShippingAddress()->getEmail(),
            "delay" => $delay,
            "first_name" => $order->getShippingAddress()->getFirstName(),
            "last_name" => $order->getShippingAddress()->getLastName(),
            "ref_code" => $this->uniqueRefCode(),
            "language" => $emailLang,
            "product_code" => $productIds
        );

        return $postData;
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @internal param Store $store
     */
    public function createInviteDataCompany($order)
    {
        $delay = $this->helperData->getKiyohDelay();
        $location_id = $this->helperData->getKiyohLocationId();
        $emailLang = $this->helperData->getKiyohEmailLang($order->getStoreId());

        $postData = array(
            "location_id" => $location_id,
            "invite_email" => $order->getShippingAddress()->getEmail(),
            "delay" => $delay,
            "first_name" => $order->getShippingAddress()->getFirstName(),
            "last_name" => $order->getShippingAddress()->getLastName(),
            "ref_code" => $this->uniqueRefCode(),
            "language" => $emailLang,
        );

        return $postData;
    }

    protected function uniqueRefCode()
    {
        return md5(uniqid(rand(), true));
    }
}
