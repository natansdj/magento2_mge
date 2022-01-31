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
use Webkul\MpIndonesianShipping\Api\CountryRepositoryInterface;
use Webkul\MpIndonesianShipping\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Webkul\MpIndonesianShipping\Api\Data\CountryInterfaceFactory;
use Webkul\MpIndonesianShipping\Model\ResourceModel\Country as ResourceCountry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\MpIndonesianShipping\Api\Data\CountrySearchResultsInterfaceFactory;
use Webkul\MpIndonesianShipping\Model\CountryFactory;

class CountryRepository implements CountryRepositoryInterface
{
    /**
     * @var Webkul\MpIndonesianShipping\Model\ResourceModel\Country
     */
    protected $resource;

    /**
     * @var Webkul\MpIndonesianShipping\Api\Data\CountryInterfaceFactory
     */
    protected $dataCountryFactory;

    /**
     * @var Webkul\MpIndonesianShipping\Api\Data\CountrySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Webkul\MpIndonesianShipping\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;

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
     * @var \Webkul\MpIndonesianShipping\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @param ResourceCountry $resource
     * @param CountryFactory $countryFactory
     * @param CountryInterfaceFactory $dataCountryFactory
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param CountrySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCountry $resource,
        CountryFactory $countryFactory,
        CountryInterfaceFactory $dataCountryFactory,
        CountryCollectionFactory $countryCollectionFactory,
        CountrySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->countryFactory = $countryFactory;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCountryFactory = $dataCountryFactory;
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
        \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
    ) {
        $countryData = $this->extensibleDataObjectConverter->toNestedArray(
            $country,
            [],
            \Webkul\MpIndonesianShipping\Api\Data\CountryInterface::class
        );

        $countryModel = $this->countryFactory->create()->setData($countryData);

        try {
            $this->resource->save($countryModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the country: %1',
                $exception->getMessage()
            ));
        }
        return $countryModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($countryId)
    {
        $country = $this->countryFactory->create();
        $this->resource->load($country, $countryId);
        if (!$country->getId()) {
            throw new NoSuchEntityException(__('Country with id "%1" does not exist.', $countryId));
        }
        return $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->countryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\MpIndonesianShipping\Api\Data\CountryInterface::class
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
        \Webkul\MpIndonesianShipping\Api\Data\CountryInterface $country
    ) {
        try {
            $countryModel = $this->countryFactory->create();
            $this->resource->load($countryModel, $country->getCountryId());
            $this->resource->delete($countryModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Country: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($countryId)
    {
        return $this->delete($this->getById($countryId));
    }
}
