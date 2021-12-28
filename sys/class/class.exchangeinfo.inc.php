<?php

/*!
 *
 * https://www.php.net/manual/en/function.apcu-cache-info.php
 * https://github.com/mohamadmulhem/php-apcu-cachingmanager/blob/master/CachingManager.php
 * https://www.php.net/manual/en/function.curl-multi-init.php
 * 
 */


/*
 Call HNB api only once, valuta=...&&valuta=...&&...
 dont build queryes every time user access website, build them once through admin panel and store them to database or memchached


 currently after disabling cryptocurrency, there still must 2 mins pass for cache to expire, but code
 refuses to behave in expected way, problem solved :)


 */
class exchangeinfo
{


    private $btc_kn = null;
    private $eth_kn = null;
    private $twt_kn = null;

    private $headers = [
        'accept: application/json'
    ];

    //COIN SYMBOLS
    private $cryptos = array();

    //HNB setup
    private $hnb_url = array();
    private static $hnb_base_url = 'https://api.hnb.hr/tecajn/v1';
    private static $hnb_parameter = 'valuta';

    //Binance setup
    private $binance_url = array();
    private static $binance_base_url = 'https://api.binance.com/api/v3/ticker/price';
    private static $binance_parameter = 'symbol';



    private $cryptoCodenames = null;

    public function __construct()
    {


        // bilo bi lipo da ucitam objekte aktivnih valuta a ne ovaj cirkus...

        $cryptocoins = new cryptocoin();
        $cryptosRes = $cryptocoins->getOnlyCodeOfActive();
        $this->cryptoCodenames = $cryptosRes["items"];




        $this->binance_url = $this->getUrls($this->cryptoCodenames, self::$binance_base_url, self::$binance_parameter);

        $this->hnb_url = $this->getUrls(FIATS, self::$hnb_base_url, self::$hnb_parameter);
        $this->cryptos = $this->getCoinsSymb($this->cryptoCodenames);
    }

    public function getCoins()
    {
        return $this->cryptos;
    }

    public function getBinanceUrls()
    {
        return $this->binance_url;
    }

    public function getHnbUrls()
    {
        return $this->hnb_url;
    }

    private function getCoinsSymb($cryptos = [])
    {
        $symbols = array();
        array_push($symbols, 'USDT');
        foreach ($cryptos as $coin) {
            $symbol = substr($coin, 0, -4); //extract coin symbol from binance api symbol
            array_push($symbols, $symbol);
        }
        return $symbols;
    }

    private function getUrls($currencies = [], $base_url, $parameter)
    {
        $URLs = array();
        foreach ($currencies as $currency) {
            $url = $this->getUrl($base_url, $parameter, $currency);
            array_push($URLs, $url);
        }
        return $URLs;
    }

    private function getUrl($url, $param_name, $param)
    {
        $parameters = [
            $param_name => $param
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        return "{$url}?{$qs}";
    }

    public function floatvalue($val)
    {
        $val = str_replace(",", ".", $val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }


    //fetches informations from desired apis and stores them in cache
    private function fetch()
    {
        //clear cache
        apcu_clear_cache();

        $ch_hnb = array();
        foreach ($this->hnb_url as $url) {

            $ch = curl_init();

            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,            // set the request URL
                CURLOPT_HTTPHEADER => $this->headers,     // set the headers 
                CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
            ));

            array_push($ch_hnb, $ch);
        }

        $ch_bin = array();
        foreach ($this->binance_url as $url) {

            $ch = curl_init();

            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,            // set the request URL
                CURLOPT_HTTPHEADER => $this->headers,     // set the headers 
                CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
            ));

            array_push($ch_bin, $ch);
        }

        $mh = curl_multi_init();

        foreach ($ch_hnb as $ch) {
            curl_multi_add_handle($mh, $ch);
        }
        foreach ($ch_bin as $ch) {
            curl_multi_add_handle($mh, $ch);
        }

        //execute the multi handle
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);

        // check every response status
        $responseStatus = false;

        while ($a = curl_multi_info_read($mh)) {
            // if ($b = $a['result']) {
            //echo ("Error message: " . curl_strerror($b)); # CURLE_* error
            //}
            if (!($b = curl_getinfo($a['handle'], CURLINFO_RESPONSE_CODE))) {
                //echo ('connection failed');
                $responseStatus = false;
                break;
            } else if ($b !== 200) {
                //echo ('HTTP status is not 200 OK');
                $responseStatus = false;
                break;
            } else {
                $responseStatus = true;
            }
        }

        foreach ($ch_hnb as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
        foreach ($ch_bin as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }

        curl_multi_close($mh);

        //return if any of API URL requests failed
        if (!$responseStatus)
            return false;

        $responses_hnb0 = array();
        foreach ($ch_hnb as $ch) {
            $response = json_decode(curl_multi_getcontent($ch));
            array_push($responses_hnb0, $response);
        }

        $responses_bin = array();
        foreach ($ch_bin as $ch) {
            $response = json_decode(curl_multi_getcontent($ch));
            array_push($responses_bin, $response);
        }

        foreach ($responses_hnb0 as $res_hnb) { //trenutno samo za USD
            //[0] jer hnb vraca ka array, pogotovo kad zatrazimo vise valuta
            $fiat_symb = $res_hnb[0]->{'Valuta'};
            $fiat_buy = $this->floatvalue($res_hnb[0]->{'Kupovni za devize'});
            $fiat_sell = $this->floatvalue($res_hnb[0]->{'Prodajni za devize'});

            if ($fiat_symb == 'USD') {
                apcu_store("USDT_BUY", $fiat_buy, APCU_TTL);
                apcu_store("USDT_SELL", $fiat_sell, APCU_TTL);
            }

            foreach ($responses_bin as $res_bin) {
                $crypto_buy = $res_bin->price * $fiat_buy;
                $crypto_sell = $res_bin->price * $fiat_sell;
                $crypto_symb = $res_bin->symbol;

                //remove USDT from name
                $symbol = substr($crypto_symb, 0, -4); // . '_' . $fiat_symb;

                apcu_store($symbol . '_BUY', $crypto_buy, APCU_TTL);
                apcu_store($symbol . '_SELL', $crypto_sell, APCU_TTL);
            }
        }

        return true;
    }

    private $values_at_user = array();

    //get all exchange infos
    public function get()
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $iterator = new APCUIterator('/^[a-zA-Z]+(_SELL|_BUY)$/');

        $success = false;

        //if values don't exist in cache, add them
        if (($iterator->getTotalCount() - 2) / 2 != count($this->cryptoCodenames)) {
            if ($success = $this->fetch())
                $iterator = new APCUIterator('/^[a-zA-Z]+(_SELL|_BUY)$/');
            else
                $result['error_code'] = API_CONNECTION_FAILED;
        } else
            $success = true;


        if ($success && ($iterator->getTotalCount() - 2) / 2 == count($this->cryptoCodenames)) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            foreach ($iterator as $counter) {
                $itemInfo = array(
                    'key' => $counter['key'],
                    'value' => $counter['value']
                );
                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
            $this->values_at_user = $result['items'];
        } else
            $result['error_code'] = COULDNT_GET_KEY_FROM_CACHE;

        return $result;
    }

    // getOne last fetch, object value, specific crypto exchange info
    public function getStored($crypto_symb)
    {
        // get() needs to be called before using this function
        if (empty($this->values_at_user)) {
            return null;
        }

        //Iterate to specific value
        $column = array_column($this->values_at_user, 'key');
        $var = array_search($crypto_symb, $column);
        $price = $this->values_at_user[$var]['value'];

        return $price;
    }

    //getOne cached or fetch new specific crypto exchange info
    public function getOne($crypto_symb)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        $success = false;

        //if value doesn't exist in cache, fetch all
        if (!apcu_exists($crypto_symb)) {
            $success = $this->fetch();
            if (!$success)
                $result['error_code'] = API_CONNECTION_FAILED;
        } else {
            $success = true;
        }

        if ($success) {
            $value = apcu_fetch($crypto_symb, $success);
            if ($success)
                $result['error_code'] = COULDNT_GET_KEY_FROM_CACHE;
        }

        if ($success) {
            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;
            $result += array(
                $crypto_symb => $value,
            );
        }

        return $result;
    }
}
