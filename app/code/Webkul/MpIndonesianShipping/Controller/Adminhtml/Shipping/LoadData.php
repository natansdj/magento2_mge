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

class LoadData extends \Magento\Backend\App\Action
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
        $resultCode = -1;

        $res = $this->_indoHelper->saveCitiesToDb();

        if ($res == null) {
            $resultCode = 0;
        } else {
            if ($res != true && $res['status']['code'] == "400") {
                $resultCode = 1;
            } else {
                if ($this->isNotStarterApi()) {
                    $res = $this->_indoHelper->saveCountriesToDb();

                    if ($res == null) {
                        $resultCode = 0;
                    } else {
                        if ($res['status']['code'] == "400") {
                            $resultCode = 1;
                        } else {
                            $resultCode = 2;
                        }
                    }
                } else {
                    $resultCode = 2;
                }
            }
        }

        if ($resultCode == 0) {
            $data['status'] = 'failed';
            $data['message'] = __('api url is not set properly');
        } elseif ($resultCode == 1) {
            $data['status'] = 'failed';
            $data['message'] = __('api key is not set properly');
        } elseif ($resultCode == 2) {
            $data['status'] = 'success';
            $data['message'] = __('Data saved properly');
        }

        $result = $result->setData(['result' => json_encode($data)]);
        return $result;
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
