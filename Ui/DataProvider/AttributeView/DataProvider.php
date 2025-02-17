<?php
/**
 * Attribute View DataProvider
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

namespace TextMaster\TextMaster\Ui\DataProvider\AttributeView;

use TextMaster\TextMaster\Model\ResourceModel\AttributeView\CollectionFactory;
use TextMaster\TextMaster\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use TextMaster\TextMaster\Model\AttributeView;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $dataPersistor,
            AttributeView::CACHE_TAG,
            $meta,
            $data
        );
    }
}
