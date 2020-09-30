<?php

namespace DWD\KiyohReviews\Model\Send;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\DataOrderCollect;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProductReviewSender
{
    protected $configScopeConfigInterface;

    protected $helperData;

    protected $helperLogger;

    protected $productInvite;

    public function __construct(
        DataOrderCollect $productInvite,
        HelperData $helperData,
        ScopeConfigInterface $configScopeConfigInterface,
        Logger $helperLogger
    )
    {
        $this->configScopeConfigInterface = $configScopeConfigInterface;
        $this->helperLogger = $helperLogger;
        $this->helperData = $helperData;
        $this->productInvite = $productInvite;
    }

    public function sendReview($order, $testMode = false)
    {
        if ($order) {
            $postData = $this->productInvite->createInviteDataProduct($order);
            $url = $this->helperData->getKiyohInviteUrl();
            $apiKey = $this->helperData->getKiyohApiKey();
            $jsonData = json_encode($postData);

            if ($testMode) {
                var_dump($postData);
                return;
            }

            try {
                // set URL and other appropriate options
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POSTFIELDS => $jsonData,
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "X-Publication-Api-Token: " . $apiKey,
                    ),
                ));
                $response = curl_exec($curl);

                $this->helperLogger->customLog(
                    'DWD Curl info: ' . $jsonData
                );
                if (curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
                    $this->helperLogger->customLog(
                        'DWD Curl error: ' . $response
                    );
                } else {
                    $this->helperLogger->customLog(
                        'DWD invite success: ' . $jsonData
                    );
                }
            } catch (\Exception $e) {
                $this->helperLogger->customLog(
                    'DWD Curl error: ' . $e->getMessage()
                );
            }
            curl_close($curl);
        }
    }
}
