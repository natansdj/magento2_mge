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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Webkul\Marketplace\Model\ControllersRepository;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var ControllersRepository
     */
    private $controllersRepository;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory  $attributeSetFactory
     */
    public function __construct(
        ControllersRepository $controllersRepository
    ) {
        $this->controllersRepository = $controllersRepository;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /**
         * insert sellerstorepickup controller's data
         */
        $data = [];

        if (empty($this->controllersRepository->getByPath('mpindonesianshipping/shipping/view'))) {
            $data[] = [
                'module_name' => 'Webkul_MpIndonesianShipping',
                'controller_path' => 'mpindonesianshipping/shipping/view',
                'label' => 'Manage Indonesian Shipping',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        $setup->getConnection()
            ->insertMultiple($setup->getTable('marketplace_controller_list'), $data);

        $setup->endSetup();
    }
}
