<?php

namespace SilkSoftwareHouse\BsgPhp\BSG;

class HLRApiClient extends ApiClient {

    protected $tariff;

    public function __construct($api_key, $tariff=null)
    {
        $this->tariff = $tariff;
        parent::__construct($api_key);
    }

    private function getStatus ($endpoint)
    {
        try {
            $resp = $this->sendRequest($endpoint);
        } catch (\Exception $e) {
            $error = 'Request failed (code: ' .$e->getCode() .'): ' . $e->getMessage();
            throw new \Exception ($error, -1);
        }

        return json_decode($resp,true);
    }

    public function getStatusByReference ($reference)
    {
        return $this->getStatus ('hlr/reference/' . $reference);
    }

    public function getStatusById ($message_id)
    {
        return $this->getStatus ('hlr/' . $message_id);
    }

    public function getPrices ($tariff=NULL)
    {
        try {
            $resp = $this->sendRequest('hlr/prices' . ($tariff !== NULL ? ('/' . $tariff) : ''));
        } catch (\Exception $e) {
            $error = 'Request failed (code: ' .$e->getCode() .'): ' . $e->getMessage();
            throw new \Exception ($error, -1);
        }
        $result = json_decode($resp,true);
        return $result;
    }

    public function sendHLR ($msisdn, $reference, $tariff=NULL)
    {
        $tariff = $tariff ?: $this->tariff;
        $message = [];
        $message['destination'] = 'phone';
        $message['msisdn'] = $msisdn;
        $message['reference'] = $reference;
        if ($tariff !== NULL)
            $message['tariff'] = $tariff;
        try {
            $resp = $this->sendRequest('hlr/create',$message);
        } catch (\Exception $e) {
            $error = 'Request failed (code: ' .$e->getCode() .'): ' . $e->getMessage();
            throw new \Exception ($error, -1);
        }
        $result = json_decode($resp,true);
        return $result;
    }

    /**
     * sends multiply HLR requests. $payload must contain array of arrays:
     * [$msisdn, $reference, $tariff, $callback_url], where $tariff and $callback_url
     * are optional
     * @param $payload
     * @return array
     * @throws \Exception
     */
    public function sendHLRs ($payload)
    {
        try {
            $resp = $this->sendRequest('hlr/create',json_encode($payload),'PUT');
        } catch (\Exception $e) {
            $error = 'Request failed (code: ' .$e->getCode() .'): ' . $e->getMessage();
            throw new \Exception ($error, -1);
        }
        $result = json_decode($resp,true);
        return $result;
    }
}