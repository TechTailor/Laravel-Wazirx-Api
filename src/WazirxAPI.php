<?php

namespace TechTailor\WazirxApi;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use TechTailor\WazirxApi\Traits\HandlesResponseErrors;

class WazirxAPI
{
    use HandlesResponseErrors;

    protected $api_key;             // API key
    protected $api_secret;          // API secret
    protected $api_url;             // API base URL
    protected $recvWindow;          // API receiving window
    protected $synced = false;
    protected $response = null;
    protected $no_time_needed = [
        'v1/time',
        'v1/systemStatus',
        'v1/exchangeInfo',
        'v1/tickers/24hr',
        'v1/ticker/24hr',
    ];

    /**
     * Constructor for WazirxAPI.
     * @param string  $key     API key
     * @param string  $secret  API secret
     * @param string  $api_url API base URL (see config for example)
     * @param integer $timing  Wazirx API timing setting (default 10000)
     */
    public function __construct($api_key = null, $api_secret = null, $api_url = null, $timing = 10000)
    {
        $this->api_key        = (! empty($api_key)) ? $api_key : config('wazirx-api.auth.key');
        $this->api_secret     = (! empty($api_secret)) ? $api_secret : config('wazirx-api.auth.secret');
        $this->api_url        = (! empty($api_url)) ? $api_url : config('wazirx-api.urls.sapi');
        $this->recvWindow = (! empty($timing)) ? $timing : config('wazirx-api.settings.timing');
    }

    /**
     * API Key and Secret Key setter function.
     * It's required for USER_DATA endpoints.
     * https://docs.wazirx.com/#endpoint-security-type
     *
     * @param string $key    API Key
     * @param string $secret API Secret
     */
    public function setAPI($api_key, $api_secret)
    {
       $this->api_key    = $api_key;
       $this->api_secret = $api_secret;
    }

    //------ PUBLIC API CALLS --------
    //---- Security Type : NONE ------
    /*
    * getTime
    * getServerStatus
    * getExchangeInfo
    * getTickers
    * getTicker
    */

    /**
     * Get Wazirx Server Time.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTime()
    {
        return $this->publicRequest('v1/time');
    }

    /**
     * Get Wazirx Server Status.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getServerStatus()
    {
        return $this->publicRequest('v1/systemStatus');
    }

    /**
     * Get Wazirx Exchange Info.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getExchangeInfo()
    {
        return $this->publicRequest('v1/exchangeInfo');
    }

    /**
     * Get Wazirx 24hr Tickers Price Change Statistics.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTickers()
    {
        return $this->publicRequest('v1/tickers/24hr');
    }

    /**
     * Get Wazirx 24hr Ticker of a particular symbol.
     *
     * @param string $symbol   Crypto Coin Pair Symbol
     * @return mixed
     * @throws \Exception
     */
    public function getTicker($symbol)
    {
        $data = [
            'symbol' => $symbol
        ];

        return $this->publicRequest('v1/ticker/24hr', $data);
    }

    //------ PRIVATE API CALLS ----------
    //--- Security Type : USER_DATA -----
    /*
    * getAccountInfo
    * getFunds
    * getAllOrders
    * getOpenOrders
    * getOrderStatus
    */

    /**
     * Get current account information
     *
     * @param  integer $iterator Number of times we've tried to get balances.
     * @param  integer $max      Max times to retry the call.
     * @return mixed
     * @throws \Exception
     */
    public function getAccountInfo($iterator = 1, $max = 3)
    {
        $response = $this->privateRequest('v1/account');

        return $response;
    }

    /**
     * Get fund details for the current account
     *
     * @param  integer $iterator Number of times we've tried to get balances.
     * @param  integer $max      Max times to retry the call.
     * @return mixed
     * @throws \Exception
     */
    public function getFunds($iterator = 1, $max = 3)
    {
        $response = $this->privateRequest('v1/funds');

        return $response;
    }

    /**
     * Get all account orders; "idle", "wait", "cancel" or "done".
     *
     * @param  integer $iterator Number of times we've tried to get balances.
     * @param  integer $max      Max times to retry the call.
     * @param string $symbol Crypto Coin Pair Symbol
     * @return mixed
     * @throws \Exception
     */
    public function getAllOrders($symbol, $iterator = 1, $max = 3)
    {
        $params = [
            'symbol' => strtolower($symbol)
        ];

        $response = $this->privateRequest('v1/allOrders', $params);

        return $response;
    }

    /**
     * Get all open orders on a symbol for the current account
     *
     * @param  integer $iterator Number of times we've tried to get balances.
     * @param  integer $max      Max times to retry the call.
     * @return mixed
     * @throws \Exception
     */
    public function getOpenOrders($iterator = 1, $max = 3)
    {
        $response = $this->privateRequest('v1/openOrders', []);

        return $response;
    }

    /**
     * Check an order's status for the current account
     *
     * @param  integer $iterator Number of times we've tried to get balances.
     * @param  integer $max      Max times to retry the call.
     * @return mixed
     * @throws \Exception
     */
    public function getOrderStatus($orderId, $iterator = 1, $max = 3)
    {
        $params = [
            'orderId' => $orderId
        ];

        $response = $this->privateRequest('v1/order', $params);

        return $response;
    }

    /**
     * Make public requests (Security Type: NONE).
     *
     * @param string $url    URL Endpoint
     * @param array $params  Required and optional parameters
     * @param string $method GET, POST, PUT, DELETE
     * @return mixed
     * @throws \Exception
     */
    private function publicRequest($url, $params = [], $method = 'GET')
    {
        // Build the POST data string
        if (! in_array($url, $this->no_time_needed))
        {
            $params['timestamp']  = $this->milliseconds();
            $params['recvWindow'] = $this->recvWindow;
        }

        $url = $this->api_url . $url;

        // Adding parameters to the url.
        $url = $url . '?' . http_build_query($params);

        return $this->sendApiRequest($url, $method);
    }

    /**
     * Make public requests (Security Type: USER_DATA).
     *
     * @param string $url    URL Endpoint
     * @param array $params  Required and optional parameters.
     *
     */
    private function privateRequest($url, $params = [], $method = 'GET')
    {
        // Build the POST data string
        if (! in_array($url, $this->no_time_needed))
        {
            $params['recvWindow'] = $this->recvWindow;
            $params['timestamp']  = $this->milliseconds();
        }

        // Build the query to pass through.
        $query   = http_build_query($params, '', '&');

        // Set API key and sign the message
        $signature    = hash_hmac('sha256', $query, $this->api_secret);

        $url = $this->api_url . $url . '?' . $query . '&signature=' . $signature;

        return $this->sendApiRequest($url, $method);
    }

    /**
     * Send request to Wazirx API for Public or Private Requests.
     *
     * @param string $url    URL Endpoint with Query & Signature
     * @param string $method GET, POST, PUT, DELETE
     * @return mixed
     * @throws \Exception
     */
    private function sendApiRequest($url, $method)
    {
        try {
            if ($method == 'POST')
                $response = Http::withHeaders([
                                'X-API-KEY' => $this->api_key
                            ])->post($url);

            elseif ($method == 'GET')
                $response = Http::withHeaders([
                                'X-API-KEY' => $this->api_key
                            ])->get($url);
        }
        catch (ConnectionException $e) {
            return $error = [
                'code' => $e->getCode(),
                'error' => 'Host Not Found',
                'message' => 'Could not resolve host: ' . $this->api_url
            ];
        }
        catch (Exception $e) {
            return $error = [
                'code' => $e->getCode(),
                'error' => 'cUrl Error',
                'message' => $e->getMessage()
            ];
        }

        // If response if Ok. Return collection.
        if($response->ok())
            return $response->collect();
        else
            return $this->handleError($response);
    }

    /**
     * Get the milliseconds from the system clock.
     * @return integer
     */
    private function milliseconds()
    {
        list ($msec, $sec) = explode (' ', microtime ());

        return $sec . substr ($msec, 2, 3);
    }
}
