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
namespace Webkul\MpIndonesianShipping\Controller\Shipping;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

class Index extends Action
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $_customerMapper;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $_customerDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Webkul\MpIndonesianShipping\Helper\Data
     */
    protected $_indoHelper;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Customer\Model\UrlFactory $urlFactory
     * @param \Webkul\MpIndonesianShipping\Helper\Data $indoHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Model\Customer\MapperFactory $customerMapper,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Customer\Model\UrlFactory $urlFactory,
        \Webkul\MpIndonesianShipping\Helper\Data $indoHelper
    ) {
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_encryptor = $encryptor;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_urlFactory = $urlFactory;
        $this->_indoHelper = $indoHelper;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session object.
     * @return \Magento\Customer\Model\SessionFactory
     */
    protected function _getSession()
    {
        return $this->_customerSessionFactory->create();
    }

    /**
     * Check customer authentication.
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_urlFactory->create()->getLoginUrl();

        if (!$this->_customerSessionFactory->create()->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Save Seller's Indonesian configuration Data.
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $customerData = $this->getRequest()->getParams();
            $customerId = $this->_getSession()->getCustomerId();
            $savedData = $this->_customerRepository->getById($customerId);
            $customer = $this->_customerDataFactory->create();
            $flatArray = $this->_customerMapper->create()->toFlatArray($savedData);

            $customerData['id'] = $customerId;
            $customerData['mp_indonesian_api_url'] = strip_tags($customerData['mp_indonesian_api_url']);

            if (isset($customerData['mp_indonesian_api_url']) && isset($flatArray['mp_indonesian_origin_id'])) {
                if ($customerData['mp_indonesian_api_url'] != \Webkul\MpIndonesianShipping\Helper\Data::PRO_API) {
                    $customerData['mp_indonesian_origin_id'] = $flatArray['mp_indonesian_origin_id'];
                }
            }

            $customerData = $this->saveDomesticCouriersAsJson($customerData);
            $customerData = $this->saveInternationalCouriersAsJson($customerData);
            $customerData = $this->checkAndSaveApiKey($customerData, $flatArray);
            $customerData['mp_indonesian_origin_type'] = strip_tags(
                $customerData['mp_indonesian_origin_type']
            );

            $customerData = array_merge(
                $flatArray,
                $customerData
            );

            $this->_dataObjectHelper->populateWithArray(
                $customer,
                $customerData,
                \Magento\Customer\Api\Data\CustomerInterface::class
            );

            try {
                $this->_customerRepository->save($customer);
                $this->checkAndSaveCountries($customerData);
                $this->messageManager->addSuccess(__('Indonesian Shipping details saved successfully.'));
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception('Can not save the records.');
            }

            return $this->resultRedirectFactory->create()
                ->setPath(
                    'mpindonesian/shipping/view',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
        }

        return $this->resultRedirectFactory->create()
            ->setPath(
                'mpindonesian/shipping/view',
                ['_secure' => $this->getRequest()->isSecure()]
            );
    }

    /**
     * check and save countries from seller credentials
     * @return void
     */
    private function checkAndSaveCountries($customerData)
    {
        if ($customerData['mp_indonesian_api_url'] != \Webkul\MpIndonesianShipping\Helper\Data::STARTER_API) {
            $this->_indoHelper->setSellerIdForCalc($customerData['id']);
            if ($customerData['mp_indonesian_api_url'] == \Webkul\MpIndonesianShipping\Helper\Data::BASIC_API) {
                $this->_indoHelper->setSellerApiUrlForCalc('basic');
            } else {
                $this->_indoHelper->setSellerApiUrlForCalc('pro');
            }
            $numCountries = $this->_indoHelper->getCountriesCount();
            $this->_indoHelper->saveCountriesToDb();
        }
    }

    /**
     * check and save domestic couriers in json format
     * @return array
     */
    private function saveDomesticCouriersAsJson($customerData)
    {
        if (isset($customerData['mp_indo_starter_dom_couriers'])) {
            if (is_array($customerData['mp_indo_starter_dom_couriers'])) {
                $customerData['mp_indo_starter_dom_couriers'] = json_encode(
                    $customerData['mp_indo_starter_dom_couriers']
                );
            }
        } else {
            $customerData['mp_indo_starter_dom_couriers'] = "";
        }

        if (isset($customerData['mp_indo_basic_dom_couriers'])) {
            if (is_array($customerData['mp_indo_basic_dom_couriers'])) {
                $customerData['mp_indo_basic_dom_couriers'] = json_encode(
                    $customerData['mp_indo_basic_dom_couriers']
                );
            }
        } else {
            $customerData['mp_indo_basic_dom_couriers'] = "";
        }

        if (isset($customerData['mp_indo_pro_dom_couriers'])) {
            if (is_array($customerData['mp_indo_pro_dom_couriers'])) {
                $customerData['mp_indo_pro_dom_couriers'] = json_encode(
                    $customerData['mp_indo_pro_dom_couriers']
                );
            }
        } else {
            $customerData['mp_indo_pro_dom_couriers'] = "";
        }

        return $customerData;
    }

    /**
     * check and save international couriers in json format
     * @return array
     */
    private function saveInternationalCouriersAsJson($customerData)
    {
        if (isset($customerData['mp_indo_basic_int_couriers'])) {
            if (is_array($customerData['mp_indo_basic_int_couriers'])) {
                $customerData['mp_indo_basic_int_couriers'] = json_encode(
                    $customerData['mp_indo_basic_int_couriers']
                );
            }
        } else {
            $customerData['mp_indo_basic_int_couriers'] = "";
        }

        if (isset($customerData['mp_indo_pro_int_couriers'])) {
            if (is_array($customerData['mp_indo_pro_int_couriers'])) {
                $customerData['mp_indo_pro_int_couriers'] = json_encode(
                    $customerData['mp_indo_pro_int_couriers']
                );
            }
        } else {
            $customerData['mp_indo_pro_int_couriers'] = "";
        }

        return $customerData;
    }

    /**
     * check and save api key
     * @return array
     */
    private function checkAndSaveApiKey($customerData, $flatArray)
    {
        $key = $customerData['mp_indonesian_api_key'];

        if ($key != '******') {
            $customerData['mp_indonesian_api_key'] = $this->_encryptor->encrypt(
                strip_tags(
                    $customerData['mp_indonesian_api_key']
                )
            );
        } else {
            $customerData['mp_indonesian_api_key'] = $flatArray['mp_indonesian_api_key'];
        }
        return $customerData;
    }
}
