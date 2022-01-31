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

namespace Webkul\MpIndonesianShipping\Model;

use Magento\Framework\Module\Dir;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Xml\Security;
use Magento\Framework\Session\SessionManager;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Webkul\MarketplaceBaseShipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Fedex\Model\Carrier as FedexCarrier;
use Webkul\MarketplaceBaseShipping\Model\ShippingSettingRepository;
use Magento\Directory\Model\CountryFactory as MagentoCountryFactory;

class Carrier extends AbstractCarrierOnline
{
    /**
     * Code of the carrier
     * @var string
     */
    const CODE = 'mpindonesianshipping';

    /**
     * Code of the carrier.
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * [$_coreSession description].
     * @var [type]
     */
    protected $_coreSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var [type]
     */
    protected $_region;

    /**
     * @var LabelGenerator
     */
    protected $_labelGenerator;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Rate result data
     * @var Result|null
     */
    protected $_result = null;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customerFactory;

    /**
     * @var string
     */
    protected $_rateServiceWsdl;

    /**
     * @var string
     */
    protected $_shipServiceWsdl;

    /**
     * @var boolean
     */
    protected $_check = false;

    /**
     * @var boolean
     */
    protected $_flag = false;

    /**
     * @var object
     */
    protected $_indoHelper;

    /**
     * @var object
     */
    protected $_rateResultFactory;

    /**
     * @var object
     */
    protected $_rateMethodFactory;

    /**
     * @var object
     */
    protected $_rateErrorFactory;

    /**
     * @var object
     */
    protected $_magentoCountryFactory;

    /**
     * @var object
     */
    protected $_marketplaceOrderFactory;

    /**
     * @var object
     */
    protected $_tracksFactory;

    /**
     * @var object
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory     $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                       $logger
     * @param Security                                                       $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory               $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory                     $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory    $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory                 $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory           $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory          $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory                         $regionFactory
     * @param \Magento\Directory\Model\CountryFactory                        $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory                       $currencyFactory
     * @param \Magento\Directory\Helper\Data                                 $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface           $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Module\Dir\Reader                           $configReader
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param SessionManager                                                 $coreSession
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param LabelGenerator                                                 $labelGenerator
     * @param \Webkul\MpIndonesianShipping\Helper\Data                       $indoHelper
     * @param array                                                          $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Webkul\Marketplace\Helper\Orders $marketplaceOrderHelper,
        \Webkul\Marketplace\Model\OrdersFactory $marketplaceOrderFactory,
        \Webkul\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Webkul\Marketplace\Model\SaleslistFactory $saleslistFactory,
        ShippingSettingRepository $shippingSettingRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Magento\Quote\Model\Quote\Item\OptionFactory $quoteOptionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        SessionManager $coreSession,
        \Magento\Framework\App\Request\Http $requestParam,
        \Magento\Customer\Model\Session $customerSession,
        LabelGenerator $labelGenerator,
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $tracksFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        MagentoCountryFactory $magentoCountryFactory,
        array $data = [],
        Json $serializer = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_tracksFactory = $tracksFactory;
        $this->_orderFactory = $orderFactory;
        $this->_marketplaceOrderFactory = $marketplaceOrderFactory;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $marketplaceOrderHelper,
            $marketplaceProductFactory,
            $saleslistFactory,
            $shippingSettingRepository,
            $productFactory,
            $addressFactory,
            $customerFactory,
            $customerSession,
            $requestParam,
            $quoteOptionFactory,
            $storeManager,
            $requestInterface,
            $httpClientFactory,
            $carrierHelper,
            $labelGenerator,
            $coreSession,
            $data
        );
        $this->_indoHelper = $indoHelper;
        $this->_magentoCountryFactory = $magentoCountryFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_rateResultFactory = $rateFactory;
        $this->_rateErrorFactory = $rateErrorFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Collect and get rates.
     * @param RateRequest $request
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error|bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates() || $this->isMultiShippingActive()) {
            return false;
        }
        $this->setRequest($request);
        return $this->getShippingPricedetail($this->_rawRequest);
    }

    /**
     * Collect and get rates.
     * @param RateRequest $request
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error|bool|Result
     */
    public function getShippingPricedetail(\Magento\Framework\DataObject $request)
    {
        if ($this->isMultiShippingActive()) {
            $this->setRequest($request);
        }

        $destCountryName = $this->_magentoCountryFactory->create()
            ->loadByCode(
                $request->getDestCountryId()
            )->getName();

        $allowSeller = $this->getConfigData('allow_seller');
        $activeCouriers = [];
        $isInternationalShipping = false;
        if ($request->getDestCountryId() != 'ID') {
            $isInternationalShipping = true;
        }

        $couriers = implode(":", $activeCouriers);
        $destPostal = $request->getDestPostal();
        $result = $this->_rateResultFactory->create();
        $priceArr = [];
        $shippinginfo = [];

        foreach ($request->getShippingDetails() as $shipDetail) {
            if ($isInternationalShipping) {
                $activeCouriers = (array)$this->_indoHelper->getActiveInternationalCarriers(
                    ($allowSeller == 1) ? $shipDetail['seller_id'] : 0
                );
            } else {
                $activeCouriers = (array)$this->_indoHelper->getActiveDomesticCarriers(
                    ($allowSeller == 1) ? $shipDetail['seller_id'] : 0
                );
            }

            $couriers = implode(":", $activeCouriers);

            $response = (array)$this->_indoHelper->getRates(
                $shipDetail,
                $destPostal,
                $couriers,
                $isInternationalShipping,
                $destCountryName,
                $request->getDestCity()
            );

            if (isset($response['error'])) {
                if ($response['error']) {
                    $error = $this->_rateErrorFactory->create();
                    $error->setCarrier($this->_code);
                    $error->setCarrierTitle($this->getConfigData('title'));
                    $error->setErrorMessage($this->getConfigData('err_msg'));
                    $result->append($error);
                    return $result;
                }
            }

            foreach ($response as $resp) {
                $priceArr[$resp['text']] = $resp['cost'];
            }

            $this->_filterSellerRate($priceArr);

            $submethod = [];
            foreach ($priceArr as $index => $price) {
                $method = $index;
                $submethod[$index] = [
                    'method' => $method,
                    'cost' => $price
                ];
            }

            array_push(
                $shippinginfo,
                [
                    'seller_id' => $shipDetail['seller_id'],
                    'methodcode' => $this->_code,
                    'submethod' => $submethod
                ]
            );
        }

        $shippingAll = [];
        $shippingAll[$this->_code] = $shippinginfo;
        $this->setShippingInformation($shippingAll);

        foreach ($this->_totalPriceArr as $methodName => $methodPrice) {
            $methodPrice = $this->_indoHelper->changePrice($methodPrice);
            $rate = $this->_rateMethodFactory->create();
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod($methodName);
            $rate->setMethodTitle($methodName);
            $rate->setCost($methodPrice);
            $rate->setPrice($methodPrice);
            $result->append($rate);
        }

        return $result;
    }

    /**
     * Api doesn't support shipment feature
     * Abstract method must be override
     * Indonesian Api doesn't support shipment
     * @param \Magento\Framework\DataObject $request
     * @return void
     * @codingStandardsIgnoreStart
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
    }

    /**
     * @codingStandardsIgnoreEnd
     * Get tracking
     * @param string|string[] $trackings
     * @return Result|null
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }
        $this->_getXmlTrackingInfo($trackings);
        return $this->_result;
    }

    /**
     * Send request for tracking
     * @param string[] $trackings
     * @return void
     */
    protected function _getXmlTrackingInfo($trackings)
    {
        foreach ($trackings as $tracking) {
            $collection = $this->loadShipTrackCollection($tracking);
            $orderId = $collection->getOrderId();
            $order = $this->loadOrder($orderId);
            $shippingMethod = $order->getShippingMethod();
            $marketplaceOrder = $this->_marketplaceOrderFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $orderId);

            $sellerId = $marketplaceOrder->getData()[0]['seller_id'];
            $parts = (array)explode(" ", $shippingMethod);
            $isParse = false;

            if (isset($parts[0])) {
                if (strpos($parts[0], $this->_code) === 0) {
                    $methodName = substr($parts[0], strpos($parts[0], "_")+1);
                    $this->_parseXmlTrackingResponse($tracking, $methodName, $sellerId);
                    $isParse = true;
                }
            }

            if (!$isParse) {
                $result = $this->_trackFactory->create();
                $track = $this->_trackStatusFactory->create();
                $track->setCarrier($this->_code);
                $track->setCarrierTitle($this->getConfigData('title'));
                $track->setTracking($tracking);
                $track->setTrackSummary(
                    'Sorry, something went wrong. Please try again or contact us and we\'ll try to help.'
                );
                $result->append($track);
                $this->_result = $result;
            }
        }
    }

    /**
     * load order
     * @param int $orderId
     * @return object
     */
    protected function loadOrder($orderId)
    {
        return $this->_orderFactory->create()->load($orderId);
    }

    /**
     * load shipment track collection
     * @param int $orderId
     * @return object
     */
    protected function loadShipTrackCollection($tracking)
    {
        return $this->_tracksFactory->create()->getCollection()
            ->addFieldToFilter('carrier_code', ['eq' => $this->_code])
            ->addFieldToFilter('track_number', ['eq' => $tracking])
            ->getFirstItem();
    }

    /**
     * Parse xml tracking response
     * @param string $trackingvalue
     * @param string $response
     * @return void
     */
    protected function _parseXmlTrackingResponse($trackingvalue, $methodName, $sellerId)
    {
        $resultArr = [];
        $result = $this->_trackFactory->create();
        $this->_indoHelper->setSellerIdForCalc($sellerId);
        $url = $this->_indoHelper->getApiUrl($sellerId);
        if ($url == \Webkul\MpIndonesianShipping\Helper\Data::BASIC_API) {
            $this->_indoHelper->setSellerApiUrlForCalc('basic');
        } elseif ($url == \Webkul\MpIndonesianShipping\Helper\Data::PRO_API) {
            $this->_indoHelper->setSellerApiUrlForCalc('pro');
        }

        $trackedData = $this->_indoHelper->getTrackedData($trackingvalue, $methodName);
        $tracking = $this->_trackStatusFactory->create();
        $tracking->setCarrier($this->_code);
        $tracking->setCarrierTitle($this->getConfigData('title'));
        $tracking->setTracking($trackingvalue);

        if ($trackedData['rajaongkir']['status']['code'] == 200) {
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingvalue);

            try {
                $trackingInfo = $this->getTrackingInfoTable($trackedData);
                $tracking->addData($trackingInfo);
            } catch (\Exception $e) {
                $tracking->setTrackSummary(
                    'Sorry, something went wrong. Please try again or contact us and we\'ll try to help.'
                );
            }
        } else {
            $tracking->setTrackSummary(
                'Sorry, something went wrong. Please try again or contact us and we\'ll try to help.'
            );
        }

        $result->append($tracking);
        $this->_result = $result;
    }

    /**
     * Get tracking information
     * @return array
     */
    public function getTrackingInfoTable($trackedData)
    {
        $result = $trackedData['rajaongkir']['result'];

        $resultArray = [
            'deliverydate' => $result['details']['waybill_date'],
            'deliverytime' => $result['details']['waybill_time'],
            'weight' => $result['details']['weight'],
            'status' => $result['summary']['status'],
            'progressdetail' => null
        ];

        foreach ($result['manifest'] as $step) {
            $item = [];
            $item['deliverylocation'] = $step['city_name'];
            $item['deliverydate'] = $step['manifest_date'];
            $item['deliverytime'] = $step['manifest_time'];
            $item['activity'] = $step['manifest_description'];
            $resultArray['progressdetail'][]  = $item;
        }

        return $resultArray;
    }

    /**
     * Check if carrier has shipping label option available
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return false;
    }
}
