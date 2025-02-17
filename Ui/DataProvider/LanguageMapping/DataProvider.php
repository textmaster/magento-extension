<?php
/**
 * Language Mapping DataProvider
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\DataProvider\LanguageMapping;

use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping\CollectionFactory;
use TextMaster\TextMaster\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use TextMaster\TextMaster\Model\LanguageMapping;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $languageMappingFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $languageMappingFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $languageMappingFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $dataPersistor,
            LanguageMapping::CACHE_TAG,
            $meta,
            $data
        );
    }
}
