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

namespace Webkul\MpIndonesianShipping\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $_customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $_attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory  $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->_customerSetupFactory = $customerSetupFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $attributeCodes = [
            ['value' => 'mp_indonesian_api_url', 'label' => __('Indonesian Api Url')],
            ['value' => 'mp_indonesian_api_key', 'label' => __('Indonesian Api Key')],
            ['value' => 'mp_indonesian_origin_type', 'label' => __('Indonesian Origin Type')],
            ['value' => 'mp_indonesian_origin_id', 'label' => __('Indonesian Origin Id')],
            ['value' => 'mp_indo_starter_dom_couriers', 'label' => __('Indonesian Starter Domestic Couriers')],
            ['value' => 'mp_indo_basic_dom_couriers', 'label' => __('Indonesian Basic Domestic Couriers')],
            ['value' => 'mp_indo_basic_int_couriers', 'label' => __('Indonesian Basic International Couriers')],
            ['value' => 'mp_indo_pro_dom_couriers', 'label' => __('Indonesian Pro Domestic Couriers')],
            ['value' => 'mp_indo_pro_int_couriers', 'label' => __('Indonesian Pro International Couriers')]
        ];

        foreach ($attributeCodes as $code) {
            $this->saveCustomerAttribute($setup, $code);
        }
    }

    /**
     * Save Customer Attribute
     * @param array
     * @return void
     */
    private function saveCustomerAttribute($setup, $code)
    {
        $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->_attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            $code['value'],
            [
                'type' => 'varchar',
                'label' => $code['label'],
                'input' => 'text',
                'frontend_class' => '',
                'required' => false,
                'visible' => false,
                'user_defined' => true,
                'sort_order' => 1000,
                'position' => 1000,
                'system' => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $code['value'])
        ->addData(
            [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [],
            ]
        );

        $attribute->save();
    }
}
