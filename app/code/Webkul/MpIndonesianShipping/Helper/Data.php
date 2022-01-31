<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpIndonesianShipping\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\CityFactory
     */
    protected $_cityFactory;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSessionFactory;

    /**
     * @var $_currencyRate
     */
    protected $_currencyRate = 0;

    /**
     * It has seller id for which calculation is doing
     * @var $_curSellerId
     */
    protected $_curSellerId = 0;

    /**
     * It has api url for which calculation is doing
     * @var $_curSellerApiUrl
     */
    protected $_curSellerApiUrl = null;

    /**
     * @var $_isInternationalShipping
     */
    protected $_isInternationalShipping = false;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Starter Api Url
     */
    const STARTER_API = 'https://api.rajaongkir.com/starter';

    /**
     * Basic Api Url
     */
    const BASIC_API = 'https://api.rajaongkir.com/basic';

    /**
     * Pro Api Url
     */
    const PRO_API = 'https://pro.rajaongkir.com/api';

    /**
     * Config Path
     */
    const XML_PATH = 'carriers/mpindonesianshipping/';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Webkul\MpIndonesianShipping\Model\CityFactory $cityFactory,
        \Webkul\MpIndonesianShipping\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_curl = $curl;
        $this->_encryptor = $encryptor;
        $this->_cityFactory = $cityFactory;
        $this->_countryFactory = $countryFactory;
        $this->_currency = $currency;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * get config value
     * @param string $field
     * @param int $storeId
     * @return int|string|boolean
     */
    public function config($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH.$field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * get admin postal code
     * @param void
     * @return int|null
     */
    public function getOriginPostalCode()
    {
        return $this->scopeConfig->getValue(
            'shipping/origin/postcode',
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    /**
     * get default weight unit
     * @param void
     * @return string
     */
    public function getDefaultWeightUnit()
    {
        return $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    /**
     * return current customer session.
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerData($customerId = 0)
    {
        return $this->_customerSessionFactory->create()->getCustomer()->load($customerId);
    }

    /**
     * get config value
     * @param string $field
     * @param int $storeId
     * @return int|string|boolean
     */
    public function isAllowedSellerToSaveDetails()
    {
        return $this->config('allow_seller');
    }

    /**
     * get the api key
     * @param boolean $forSeller
     * @return string
     */
    public function getApiKey($sellerId = 0)
    {
        if ($sellerId > 0) {
            if ($this->isAllowedSellerToSaveDetails()) {
                return $this->getCustomerData($sellerId)->getMpIndonesianApiKey();
            }
        }
        return $this->config('api_key');
    }

    /**
     * get api url
     * @param boolean $forSeller
     * @return string
     */
    public function getApiUrl($sellerId = 0)
    {
        if ($sellerId > 0) {
            if ($this->isAllowedSellerToSaveDetails()) {
                return $this->getCustomerData($sellerId)->getMpIndonesianApiUrl();
            }
        }
        return $this->config('api_url');
    }

    /**
     * get active domestic methods of shipping
     * @param int $sellerId
     * @return array
     */
    public function getActiveDomesticCarriers($sellerId)
    {
        $res = null;
        if ($sellerId) {
            switch ($this->getApiUrl($sellerId)) {
                case self::STARTER_API:
                    $res = (array)json_decode(
                        strtolower(
                            $this->getCustomerData($sellerId)->getMpIndoStarterDomCouriers()
                        )
                    );
                    break;
                case self::BASIC_API:
                    $res = (array)json_decode(
                        strtolower(
                            $this->getCustomerData($sellerId)->getMpIndoBasicDomCouriers()
                        )
                    );
                    break;
                case self::PRO_API:
                    $res = (array)json_decode(
                        strtolower(
                            $this->getCustomerData($sellerId)->getMpIndoProDomCouriers()
                        )
                    );
                    break;
                default:
                    $res = '';
            }
        } else {
            switch ($this->getApiUrl()) {
                case self::STARTER_API:
                    $res = explode(
                        ',',
                        strtolower(
                            $this->config(
                                'starter_domestic_shipping_couriers'
                            )
                        )
                    );
                    break;
                case self::BASIC_API:
                    $res = explode(
                        ',',
                        strtolower(
                            $this->config(
                                'basic_domestic_shipping_couriers'
                            )
                        )
                    );
                    break;
                case self::PRO_API:
                    $res = explode(
                        ',',
                        strtolower(
                            $this->config(
                                'pro_domestic_shipping_couriers'
                            )
                        )
                    );
                    break;
                default:
                    $res = '';
            }
        }

        return $res;
    }

    /**
     * get active international methods of shipping
     * @param int $sellerId
     * @return array
     */
    public function getActiveInternationalCarriers($sellerId)
    {
        if ($sellerId) {
            switch ($this->getApiUrl($sellerId)) {
                case self::BASIC_API:
                    $res = (array)json_decode(
                        strtolower(
                            $this->getCustomerData($sellerId)->getMpIndoBasicIntCouriers()
                        )
                    );
                    break;
                case self::PRO_API:
                    $res = (array)json_decode(
                        strtolower(
                            $this->getCustomerData($sellerId)->getMpIndoProIntCouriers()
                        )
                    );
                    break;
                default:
                    $res = '';
            }
        } else {
            switch ($this->getApiUrl()) {
                case self::BASIC_API:
                    $res = explode(
                        ',',
                        strtolower(
                            $this->config(
                                'basic_international_shipping_couriers'
                            )
                        )
                    );
                    break;
                case self::PRO_API:
                    $res = explode(
                        ',',
                        strtolower(
                            $this->config(
                                'pro_international_shipping_couriers'
                            )
                        )
                    );
                    break;
                default:
                    $res = '';
            }
        }

        return $res;
    }

    /**
     * change shipping price
     * @param int|float $price
     * @return int|float
     */
    public function changePrice($price)
    {
        $set = $this->config('add_sub_price');

        if (!$set) {
            return $price;
        } else {
            $foundPercentage = false;

            if (strpos($set, '%') !== false) {
                $foundPercentage = true;
                $set = str_replace('%', '', $set);
            }

            $foundMinus = false;

            if (strpos($set, '-') !== false) {
                $foundMinus = true;
                $set = str_replace('-', '', $set);
            }

            $foundPlus = false;

            if (strpos($set, '+') !== false) {
                $foundPlus = true;
                $set = str_replace('+', '', $set);
            }

            $finalSet = $set;
            $changedPrice = 0;

            if ($foundPercentage) {
                $changedPrice = ($price * $set) / 100;
            } else {
                $changedPrice = abs($set);
            }

            if ($foundMinus) {
                return $price - $changedPrice;
            }

            if ($foundPlus) {
                return $price + $changedPrice;
            }

            return $price;
        }
    }

    /**
     * get shipping rates
     * @param array $shipDetail                shipping details
     * @param int $dest                        store the destination zip code
     * @param string $method                   courier services
     * @param boolean $isInternationalShipping check international shipping
     * @param string $destCountryName          destination country name
     * @param string $destCity                 destination city name
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRates(
        $shipDetail,
        $dest,
        $method,
        $isInternationalShipping,
        $destCountryName = "",
        $destCity = ""
    ) {
        $sellerId = $shipDetail['seller_id'];
        $origin = $shipDetail['origin_postcode'];
        $weight = $shipDetail['items_weight'];

        if ($this->getDefaultWeightUnit() == "lbs") {
            $weight *= 453.592;
        } elseif ($this->getDefaultWeightUnit() == "kgs") {
            $weight *= 1000;
        }

        // api doesn't support decimal weight value
        $weight = round($weight);

        $postFields = "";
        $post = null;
        $originId = 0;
        $destId = 0;
        $originType = 'city';
        $destType = 'city';

        $this->_curSellerId = $sellerId;
        $this->_curSellerApiUrl = $this->getApiUrl($sellerId);
        $this->_isInternationalShipping = $isInternationalShipping;

        if (!$this->_isInternationalShipping) {
            if ($sellerId > 0) {
                if ($this->_curSellerApiUrl == self::PRO_API) {
                    if (!$this->getCustomerData()->getMpIndonesianOriginType()) {
                        $originId = $this->getCityIdByZipcode($origin);
                    } else {
                        $originType = 'subdistrict';
                        $originId = $this->getCustomerData()->getMpIndonesianOriginId();
                    }
                } else {
                    $originId = $this->getCityIdByZipcode($origin);
                }
            } else {
                if ($this->_curSellerApiUrl == self::PRO_API) {
                    if ($this->config('origin_type')) {
                        $originType = 'subdistrict';
                        $originId = $this->config('sub_district_origin');
                    } else {
                        $originId = $this->getCityIdByZipcode($origin);
                    }
                } else {
                    $originId = $this->getCityIdByZipcode($origin);
                }
            }
        } else {
            $originId = $this->getCityIdByZipcode($origin);
        }

        if ($this->_isInternationalShipping) {
            if ($this->_curSellerApiUrl != self::STARTER_API) {
                $destId = $this->getCountryIdByCountryName($destCountryName);
                $postFields = "origin=$originId".
                    "&destination=$destId".
                    "&weight=$weight".
                    "&courier=$method";
                $post = json_decode($this->requestPost('/v2/internationalCost', $postFields), true);
            }
        } else {
            $destId = $this->getCityIdByZipcode($dest);
            if ($destId == 0) {
                $destId = $this->getCityIdByCityName($destCity);
            }

            if ($this->_curSellerApiUrl == self::PRO_API) {
                $postFields = "origin=$originId".
                    "&originType=$originType".
                    "&destination=$destId".
                    "&destinationType=$destType".
                    "&weight=$weight".
                    "&courier=$method";
            } else {
                $postFields = "origin=$originId".
                    "&destination=$destId".
                    "&weight=$weight".
                    "&courier=$method";
            }

            $post = json_decode($this->requestPost('/cost', $postFields), true);
        }

        $array_rates = $this->createRatesArray($post);
        return $array_rates;
    }

    /**
     * create rates array
     * @param array
     * @param string
     * @param boolean
     * @return array
     */
    private function createRatesArray($post)
    {
        $array_rates = [];
        if (!isset($post['rajaongkir']['status']['code']) || $post['rajaongkir']['status']['code'] != '200') {
            $arr['error'] = true;
            return $arr;
        }

        if (isset($post['rajaongkir']['results'])) {
            if (($this->_curSellerApiUrl != self::STARTER_API) && ($this->_currencyRate == 0)) {
                $this->_currencyRate = $this->getCurrencyRates();
            }
            foreach ($post['rajaongkir']['results'] as $courierService) {
                $name_method = strtoupper($courierService['code']);
                foreach ($courierService['costs'] as $service) {
                    $text = $name_method.' '.'('.$service['service'].') '.($service['description'] ?? '');

                    if ($this->_isInternationalShipping) {
                        $array_rates[] = [
                            'text'=> $text,
                            'cost'=> $this->shipingRatesToUSD($service['cost'], $service['currency'])
                        ];
                    } else {
                        foreach ($service['cost'] as $cost) {
                            $array_rates[] = [
                                'text'=> $text.' '.$cost['note'],
                                'cost'=> $this->shipingRatesToUSD($cost['value'])
                            ];
                        }
                    }
                }
            }
        }

        return $array_rates;
    }

    /**
     * Convert IDR Currency to USD Currency
     * @return float
     */
    public function shipingRatesToUSD($amount = 0, $currencyCode = 'IDR')
    {
        if ($amount > 0) {
            if ($this->_curSellerApiUrl != self::STARTER_API) {
                if ($currencyCode == 'IDR') {
                    $amount = round(($amount/$this->_currencyRate), 2);
                } else {
                    $amount = round($amount, 2);
                }
            } else {
                $curCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
                $curRate = $this->_storeManager->getStore()->getBaseCurrency()->getRate($curCurrencyCode);
                $idrRate = $this->_storeManager->getStore()->getBaseCurrency()->getRate('IDR');
                if ($idrRate != 0) {
                    $amount = round(($curRate/$idrRate)*$amount, 2);
                } else {
                    $amount = 0;
                }
            }
        }
        return $amount;
    }

    /**
     * make an api call
     * @param  string $method    shipping method name
     * @param  array $postfield  remaing parameter of shipping (ex. origion,dest etc)
     * @return array|string
     */
    public function requestPost($method, $postfield)
    {
        try {
            $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->_curl->setOption(CURLOPT_ENCODING, "");
            $this->_curl->setOption(CURLOPT_MAXREDIRS, 10);
            $this->_curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->_curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $this->_curl->setOption(CURLOPT_HTTPHEADER, [
                "key: ".$this->_encryptor->decrypt($this->getApiKey($this->_curSellerId))
            ]);

            if ($this->_curSellerApiUrl == null) {
                $this->_curSellerApiUrl = $this->getApiUrl();
            }

            $url = $this->_curSellerApiUrl.$method;
            $this->_curl->post($url, $postfield);
            return $response = $this->_curl->getBody();
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Make an Api call
     * @param  string $method
     * @return array|string
     */
    public function request($method)
    {
        try {
            $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->_curl->setOption(CURLOPT_ENCODING, "");
            $this->_curl->setOption(CURLOPT_MAXREDIRS, 10);
            $this->_curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->_curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            $this->_curl->setOption(CURLOPT_HTTPHEADER, [
                "key: ".$this->_encryptor->decrypt($this->getApiKey($this->_curSellerId))
            ]);

            if ($this->_curSellerApiUrl == null) {
                $this->_curSellerApiUrl = $this->getApiUrl();
            }

            $url = $this->_curSellerApiUrl.$method;
            $this->_curl->get($url);
            return $response = $this->_curl->getBody();
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * get all cites results for api
     * @return array
     */
    public function getAllCity()
    {
        return $this->request('/city');
    }

    /**
     * get all subdistrict results for api
     * @param int $cityId
     * @param int $forAjax
     * @return array
     */
    public function getAllSubDistrict($cityId)
    {
        return $this->request('/subdistrict?city='.$cityId);
    }

    /**
     * get all international cities results for api
     * @return array
     */
    public function getAllCountry()
    {
        return $this->request('/v2/internationalDestination');
    }

    /**
     * use when need to hit api from specific seller credentials
     * @param $sellerId
     * @return void
     */
    public function setSellerIdForCalc($sellerId)
    {
        $this->_curSellerId = $sellerId;
    }

    /**
     * use when need to hit specific api url for request
     * @param $apiVersion
     * @return void
     */
    public function setSellerApiUrlForCalc($apiVersion)
    {
        switch ($apiVersion) {
            case 'starter':
                $this->_curSellerApiUrl = self::STARTER_API;
                break;
            case 'basic':
                $this->_curSellerApiUrl = self::BASIC_API;
                break;
            case 'pro':
                $this->_curSellerApiUrl = self::PRO_API;
                break;
            default:
                return '';
        }
    }

    /**
     * get tracked shipment data
     * @return array
     */
    public function getTrackedData($waybill, $courier)
    {
        $courier = strtolower($courier);
        if ($courier == "j&t") {
            $courier = "jnt";
        }
        $postFields = "waybill=$waybill&courier=$courier";
        $post = json_decode($this->requestPost('/waybill', $postFields), true);
        return $post;
    }

    /**
     * get currency rates
     * @param boolean $forseller
     * @return array
     */
    public function getCurrencyRates()
    {
        $result = (array)json_decode($this->request('/currency'), true);
        if ($result['rajaongkir']['status']['code'] == 200) {
            return $result['rajaongkir']['result']['value'];
        }
        return 0;
    }

    /**
     * get city id by zip code
     * @param  int $id
     * @return int
     */
    public function getCityIdByZipcode($zipcode)
    {
        $cityId = 0;
        $collection = $this->_cityFactory->create()->getCollection();
        $collection->addFieldToFilter('postal_code', ['eq'=>$zipcode]);
        if ($collection->getSize()) {
            foreach ($collection as $cityRecord) {
                $cityId = $cityRecord['city_id'];
            }
        }
        return $cityId;
    }

    /**
     * get city id by city name
     * @param string $cityName
     * @return int
     */
    public function getCityIdByCityName($cityName)
    {
        $cityId = 0;
        if (!empty($cityName)) {
            $collection = $this->_cityFactory->create()->getCollection();
            $collection->addFieldToFilter('city_name', ['eq' => $cityName]);
            if ($collection->getSize()) {
                foreach ($collection as $cityRecord) {
                    $cityId = $cityRecord['city_id'];
                }
            }
        }

        return $cityId;
    }

    /**
     * get country id by country name for international shipping
     * @param  int $id
     * @return int
     */
    public function getCountryIdByCountryName($countryName)
    {
        $countryName = $this->changeCountryName($countryName);
        $countryId = 0;
        $collection = $this->_countryFactory->create()->getCollection();
        $collection->addFieldToFilter('country_name', ['eq'=>$countryName]);
        if ($collection->getSize()) {
            foreach ($collection as $countryRecord) {
                $countryId=$countryRecord['country_id'];
            }
        }
        return $countryId;
    }

    /**
     * change the country name according to indonesian database
     * @return string
     */
    private function changeCountryName($countryName)
    {
        switch ($countryName) {
            case 'China':
                $countryName = 'China (people_s rep)';
                break;

            case 'United States':
                $countryName = 'United States of America';
                break;
        }

        return $countryName;
    }

    /**
     * Save Cities in Database
     * @param void
     * @return array|boolean
     */
    public function saveCitiesToDb()
    {
        $dataCity = json_decode($this->getAllCity(), true);

        if ($dataCity['rajaongkir']['status']['code'] == 200) {
            $collection = $this->_cityFactory->create()->getCollection();
            $tableName = $collection->getMainTable();
            $connection = $collection->getConnection();
            $connection->truncateTable($tableName);

            foreach ($dataCity['rajaongkir']['results'] as $apiData) {
                try {
                    $this->saveCityRecordToDb($apiData);
                } catch (\Exception $ex) {
                    $ex->getMessage();
                }
            }
            return true;
        } else {
            return $dataCity['rajaongkir'];
        }
    }

    /**
     * Actual method which save cities in database
     * @param void
     */
    private function saveCityRecordToDb($apiData)
    {
        $city = $this->_cityFactory->create();
        $city->setCityId($apiData['city_id']);
        $city->setCityName($apiData['city_name']);
        $city->setPostalCode($apiData['postal_code']);
        $city->save();
    }

    /**
     * Save Countries in Database
     * @param void
     * @return array|boolean
     */
    public function saveCountriesToDb()
    {
        $dataCountry = json_decode($this->getAllCountry(), true);

        if ($dataCountry['rajaongkir']['status']['code'] == 200) {
            $numCountries = count($dataCountry['rajaongkir']['results']);
            if ($this->getCountriesCount() != $numCountries) {
                $collection = $this->_countryFactory->create()->getCollection();
                $tableName = $collection->getMainTable();
                $connection = $collection->getConnection();
                $connection->truncateTable($tableName);

                foreach ($dataCountry['rajaongkir']['results'] as $apiData) {
                    try {
                        $this->saveCountryRecordToDb($apiData);
                    } catch (\Exception $ex) {
                        $ex->getMessage();
                    }
                }
                return true;
            } else {
                return true;
            }
        } else {
            return $dataCountry['rajaongkir'];
        }
    }

    /**
     * Actual method which save countries in database
     * @param void
     */
    private function saveCountryRecordToDb($apiData)
    {
        $country = $this->_countryFactory->create();
        $country->setCountryId($apiData['country_id']);
        $country->setCountryName($apiData['country_name']);
        $country->save();
    }

    /**
     * get number of cities available in database
     * @return int
     */
    public function getCitiesCount()
    {
        return $this->_cityFactory->create()->getCollection()->getSize();
    }

    /**
     * get number of countries available in database
     * @return int
     */
    public function getCountriesCount()
    {
        return $this->_countryFactory->create()->getCollection()->getSize();
    }

    /**
     * is cities available in database
     * @return boolean
     */
    public function isCitiesAvailable()
    {
        return ($this->getCitiesCount() > 0) ? true : false;
    }

    /**
     * is countries available in database
     * @return boolean
     */
    public function isCountriesAvailable()
    {
        return ($this->getCountriesCount() > 0) ? true : false;
    }
}
