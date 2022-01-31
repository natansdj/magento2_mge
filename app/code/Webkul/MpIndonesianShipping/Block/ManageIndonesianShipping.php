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
namespace Webkul\MpIndonesianShipping\Block;

use Magento\Catalog\Model\Product;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Webkul\MarketplaceBaseShipping\Model\ShippingSettingRepository;
use Webkul\MpIndonesianShipping\Model\System\Config\Source\StarterDomesticShippingCouriers;
use Webkul\MpIndonesianShipping\Model\System\Config\Source\BasicDomesticShippingCouriers;
use Webkul\MpIndonesianShipping\Model\System\Config\Source\BasicInternationalShippingCouriers;
use Webkul\MpIndonesianShipping\Model\System\Config\Source\ProDomesticShippingCouriers;
use Webkul\MpIndonesianShipping\Model\System\Config\Source\ProInternationalShippingCouriers;

class ManageIndonesianShipping extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_indoHelper;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $_marketplaceHelper;

    /**
     * @var ShippingSettingRepository
     */
    protected $_shippingSettingRepository;

    /**
     * @var Session
     */
    protected $_customerSessionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var StarterDomesticShippingCouriers
     */
    protected $_starterDomesticCouriers;

    /**
     * @var BasicDomesticShippingCouriers
     */
    protected $_basicDomesticCouriers;

    /**
     * @var BasicInternationalShippingCouriers
     */
    protected $_basicInternationalCouriers;

    /**
     * @var ProDomesticShippingCouriers
     */
    protected $_proDomesticCouriers;

    /**
     * @var ProInternationalShippingCouriers
     */
    protected $_proInternationalCouriers;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context        $context
     * @param \Webkul\MpIndonesianShipping\Helper\Data                $indoHelper
     * @param \Webkul\Marketplace\Helper\Data                         $marketplaceHelper
     * @param \Magento\Customer\Model\SessionFactory                  $customerSessionFactory
     * @param \Magento\Framework\Registry                             $coreRegistry
     * @param ShippingSettingRepository                               $shippingSettingRepository
     * @param StarterDomesticShippingCouriers                         $starterDomesticCouriers
     * @param BasicDomesticShippingCouriers                           $basicDomesticCouriers
     * @param BasicInternationalShippingCouriers                      $basicInternationalCouriers
     * @param ProDomesticShippingCouriers                             $proDomesticCouriers
     * @param ProInternationalShippingCouriers                        $proInternationalCouriers
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\Registry $coreRegistry,
        ShippingSettingRepository $shippingSettingRepository,
        StarterDomesticShippingCouriers $starterDomesticCouriers,
        BasicDomesticShippingCouriers $basicDomesticCouriers,
        BasicInternationalShippingCouriers $basicInternationalCouriers,
        ProDomesticShippingCouriers $proDomesticCouriers,
        ProInternationalShippingCouriers $proInternationalCouriers,
        array $data = []
    ) {
        $this->_indoHelper = $indoHelper;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_shippingSettingRepository = $shippingSettingRepository;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_starterDomesticCouriers = $starterDomesticCouriers;
        $this->_basicDomesticCouriers = $basicDomesticCouriers;
        $this->_basicInternationalCouriers = $basicInternationalCouriers;
        $this->_proDomesticCouriers = $proDomesticCouriers;
        $this->_proInternationalCouriers = $proInternationalCouriers;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * return current customer session.
     * @return \Magento\Customer\Model\SessionFactory
     */
    public function _getCustomerData()
    {
        return $this->_customerSessionFactory->create()->getCustomer();
    }

   /**
    * Retrieve information from carrier configuration.
    * @param string $field
    * @return void|false|string
    */
    public function getConfigData($field)
    {
        return $this->getHelper()->config($field);
    }

    /**
     * get current module helper
     * @return \Webkul\MpIndonesianShipping\Helper\Data
     */
    public function getHelper()
    {
        return $this->_indoHelper;
    }

    /**
     * Retrieve current order model instance
     * @return string|null
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
     * Get Currency Code for Custom Value
     * @return string
     */
    public function getCustomValueCurrencyCode()
    {
        $orderInfo = $this->getOrder();
        return $orderInfo->getBaseCurrency()->getCurrencyCode();
    }

    /**
     * Get Seller Origin Postal Code
     * @return array
     */
    public function getOriginPostalCode()
    {
        $isPartner = $this->_marketplaceHelper->isSeller();
        if ($isPartner == 1) {
            $model = $this->_shippingSettingRepository->getBySellerId(
                $this->_marketplaceHelper->getCustomerId()
            );
            return $model->getPostalCode();
        }
        return 0;
    }

    /**
     * Get All Sub Districts
     * @return array
     */
    public function getAllSubDistrict()
    {
        $cityId = $this->getCityId();
        if ($cityId > 0) {
            $helper = $this->getHelper();
            $helper->setSellerIdForCalc($this->_getCustomerData()->getId());
            $helper->setSellerApiUrlForCalc('pro');
            return $helper->getAllSubDistrict($cityId);
        } else {
            return false;
        }
    }

    /**
     * Get City Id
     * @return int
     */
    public function getCityId()
    {
        $postalCode = $this->getOriginPostalCode();
        if ($postalCode) {
            return $this->getHelper()->getCityIdByZipcode($postalCode);
        }
    }

    /**
     * Get Starter Url
     * @return string
     */
    public function getStarterUrl()
    {
        return \Webkul\MpIndonesianShipping\Helper\Data::STARTER_API;
    }

    /**
     * Get Basic Url
     * @return string
     */
    public function getBasicUrl()
    {
        return \Webkul\MpIndonesianShipping\Helper\Data::BASIC_API;
    }

    /**
     * Get Pro Url
     * @return string
     */
    public function getProUrl()
    {
        return \Webkul\MpIndonesianShipping\Helper\Data::PRO_API;
    }

    /**
     * Get All Starter Domestic Couriers
     * @return array
     */
    public function getAllStarterDomesticCouriers()
    {
        return $this->_starterDomesticCouriers->toOptionArray();
    }

    /**
     * Get All Basic Domestic Couriers
     * @return array
     */
    public function getAllBasicDomesticCouriers()
    {
        return $this->_basicDomesticCouriers->toOptionArray();
    }

    /**
     * Get All Basic International Couriers
     * @return array
     */
    public function getAllBasicInternationalCouriers()
    {
        return $this->_basicInternationalCouriers->toOptionArray();
    }

    /**
     * Get All Pro Domestic Couriers
     * @return array
     */
    public function getAllProDomesticCouriers()
    {
        return $this->_proDomesticCouriers->toOptionArray();
    }

    /**
     * Get All Pro International Couriers
     * @return array
     */
    public function getAllProInternationalCouriers()
    {
        return $this->_proInternationalCouriers->toOptionArray();
    }

    /**
     * is secure
     * @return boolean
     */
    public function isSecure()
    {
        return $this->getRequest()->isSecure();
    }
}
