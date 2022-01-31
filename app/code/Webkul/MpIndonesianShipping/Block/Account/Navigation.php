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
namespace Webkul\MpIndonesianShipping\Block\Account;

class Navigation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\Marketplace\Helper\Orders
     */
    protected $_mpOrdersHelper;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $_mpDataHelper;

    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Webkul\Marketplace\Helper\Orders $mpHelper
     * @param \Webkul\Marketplace\Helper\Data $mpDataHelper
     * @param \Webkul\MpIndonesianShipping\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Orders $mpOrdersHelper,
        \Webkul\Marketplace\Helper\Data $mpDataHelper,
        \Webkul\MpIndonesianShipping\Helper\Data $dataHelper
    ) {
        $this->_mpOrdersHelper = $mpOrdersHelper;
        $this->_mpDataHelper = $mpDataHelper;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * get marketplace helper
     * @return object
     */
    public function getMarketplaceOrderHelper()
    {
        return $this->_mpOrdersHelper;
    }

    /**
     * get marketplace helper
     * @return object
     */
    public function getMarketplaceDataHelper()
    {
        return $this->_mpDataHelper;
    }

    /**
     * get indonesian helper
     * @return object
     */
    public function getHelper()
    {
        return $this->_dataHelper;
    }

    /**
     * Is Secure
     * @return boolean
     */
    public function isSecure()
    {
        return $this->getRequest()->isSecure();
    }
}
