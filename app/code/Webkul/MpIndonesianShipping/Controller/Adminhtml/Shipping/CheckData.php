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

namespace Webkul\MpIndonesianShipping\Controller\Adminhtml\Shipping;

class CheckData extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_MpIndonesianShipping::config_MpIndonesianShipping';

    /**
     * @var \Webkul\AdvancedBookingQrcode\Helper\Data
     */
    protected $_indoHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Constructor
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Webkul\MpIndonesianShipping\Helper\Data          $indoHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_indoHelper = $indoHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Load Data into Database
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $data = [];

        $needUpdate = false;
        if (!$this->_indoHelper->isCitiesAvailable()) {
            $needUpdate = true;
        }
        if ($this->isNotStarterApi()) {
            if (!$needUpdate) {
                if (!$this->_indoHelper->isCountriesAvailable()) {
                    $needUpdate = true;
                }
            }
        }

        if ($needUpdate) {
            $result = $result->setData(['status' => 'failed']);
            return $result;
        }

        $res = $this->_indoHelper->getAllCity();
        $res = (array)json_decode($res, true);
        $needUpdate = $this->checkAndSaveCities($res, $result);

        if ($this->isNotStarterApi()) {
            if (!$needUpdate) {
                $res = $this->_indoHelper->getAllCountry();
                $res = (array)json_decode($res, true);

                $needUpdate = $this->checkAndSaveCountries($res, $result);
            }
        }

        if ($needUpdate) {
            $result = $result->setData(['status' => 'failed']);
            return $result;
        }

        $result = $result->setData(['status' => 'success']);
        return $result;
    }

    /**
     * check and save cities
     * @param array $res
     * @param object $result
     * @return boolean
     */
    private function checkAndSaveCities($res, $result)
    {
        if (!empty($res)) {
            if (isset($res['rajaongkir']['status']['code'])) {
                if ($res['rajaongkir']['status']['code'] == 200) {
                    $citiesInDatabase = $this->_indoHelper->getCitiesCount();
                    $numCities = count($res['rajaongkir']['results']);
                    if ($citiesInDatabase != $numCities) {
                        return true;
                    }
                } else {
                    $result = $result->setData(['status' => '404']);
                    return $result;
                }
            }
        } else {
            $result = $result->setData(['status' => '404']);
            return $result;
        }
        return false;
    }

    /**
     * check and save countries
     * @param array $res
     * @param object $result
     * @return boolean
     */
    private function checkAndSaveCountries($res, $result)
    {
        if (!empty($res)) {
            if (isset($res['rajaongkir']['status']['code'])) {
                if ($res['rajaongkir']['status']['code'] == 200) {
                    $countriesInDatabase = $this->_indoHelper->getCountriesCount();
                    $numCountries = count($res['rajaongkir']['results']);
                    if ($countriesInDatabase != $numCountries) {
                        return true;
                    }
                } else {
                    $result = $result->setData(['status' => '404']);
                    return $result;
                }
            }
        } else {
            $result = $result->setData(['status' => '404']);
            return $result;
        }
        return false;
    }

    /**
     * check api version is not starter
     * @param void
     * @return boolean
     */
    private function isNotStarterApi()
    {
        if ($this->_indoHelper->getApiUrl() != \Webkul\MpIndonesianShipping\Helper\Data::STARTER_API) {
            return true;
        }

        return false;
    }
}
