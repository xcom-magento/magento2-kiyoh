<?php

namespace DWD\KiyohReviews\Model\Send;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;

class ProductsSender
{
    protected $helperData;

    protected $helperLogger;

    public function __construct(
        HelperData $helperData,
        Logger $helperLogger
    )
    {
        $this->helperData = $helperData;
        $this->helperLogger = $helperLogger;
    }

    public function sendProducts($productsData)
    {
        $location_id = $this->helperData->getKiyohLocationId();

        foreach ($productsData as $productData) {
            $data = array("location_id" => $location_id, "products" => $productData);
            $this->sendCurl($data);
        }
    }

    /**
     * @param $products array
     */
    protected function sendCurl($products)
    {
        $url = $this->helperData->getKiyohInputProductBulkUrl();
        $apiKey = $this->helperData->getKiyohApiKey();
        $jsonProducts = json_encode($products);

        try {
            // set URL and other appropriate options
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER =>1,
                CURLOPT_POSTFIELDS => $jsonProducts,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "X-Publication-Api-Token: " . $apiKey,
                ),
            ));
            $response = curl_exec($curl);

            if (curl_errno($curl) ||  curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
                $this->helperLogger->customLog(
                    'DWD Curl error: ' . $response
                );
            }else{
                $this->helperLogger->customLog(
                    'Sended products success' . $response
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
