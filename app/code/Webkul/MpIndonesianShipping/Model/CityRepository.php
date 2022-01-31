<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_MpIndonesianShipping
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpIndonesianShipping\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\MpIndonesianShipping\Api\CityRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Webkul\MpIndonesianShipping\Api\Data\CitySearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Webkul\MpIndonesianShipping\Api\Data\CityInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Webkul\MpIndonesianShipping\Model\ResourceModel\City as ResourceCity;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\MpIndonesianShipping\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\MpIndonesianShipping\Model\CityFactory;

class CityRepository implements CityRepositoryInterface
{
    /**
     * @var Webkul\MpIndonesianShipping\Model\ResourceModel\City
     */
    protected $resource;

    /**
     * @var Webkul\MpIndonesianShipping\Api\Data\CityInterfaceFactory
     */
    protected $dataCityFactory;

    /**
     * @var Webkul\MpIndonesianShipping\Api\Data\CitySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\ResourceModel\City\CollectionFactory
     */
    protected $cityCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @param ResourceCity $resource
     * @param CityFactory $cityFactory
     * @param CityInterfaceFactory $dataCityFactory
     * @param CityCollectionFactory $cityCollectionFactory
     * @param CitySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCity $resource,
        CityFactory $cityFactory,
        CityInterfaceFactory $dataCityFactory,
        CityCollectionFactory $cityCollectionFactory,
        CitySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->cityFactory = $cityFactory;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCityFactory = $dataCityFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
    ) {
        $cityData = $this->extensibleDataObjectConverter->toNestedArray(
            $city,
            [],
            \Webkul\MpIndonesianShipping\Api\Data\CityInterface::class
        );

        $cityModel = $this->cityFactory->create()->setData($cityData);

        try {
            $this->resource->save($cityModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the city: %1',
                $exception->getMessage()
            ));
        }
        return $cityModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cityId)
    {
        $city = $this->cityFactory->create();
        $this->resource->load($city, $cityId);
        if (!$city->getId()) {
            throw new NoSuchEntityException(__('City with id "%1" does not exist.', $cityId));
        }
        return $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cityCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\MpIndonesianShipping\Api\Data\CityInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\MpIndonesianShipping\Api\Data\CityInterface $city
    ) {
        try {
            $cityModel = $this->cityFactory->create();
            $this->resource->load($cityModel, $city->getCityId());
            $this->resource->delete($cityModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the City: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cityId)
    {
        return $this->delete($this->getById($cityId));
    }
}
