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
namespace Webkul\MpIndonesianShipping\Block\System\Config\Form;

class LoadData extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template Path
     * @var
     */
    const BUTTON_TEMPLATE = 'system/config/load_data.phtml';

    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_indoHelper;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\MpIndonesianShipping\Helper\Data $indoHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper,
        array $data = []
    ) {
        $this->_indoHelper = $indoHelper;
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself.
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }

        return $this;
    }

    /**
     * check admin has api url
     * @return string
     */
    public function hasApiUrl()
    {
        $len = strlen($this->_indoHelper->getApiUrl());
        if ($len == 0) {
            return false;
        }

        return true;
    }

    /**
     * check admin has api key
     * @return string
     */
    public function hasApiKey()
    {
        $len = strlen($this->_indoHelper->getApiKey());
        if ($len == 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the button.
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
