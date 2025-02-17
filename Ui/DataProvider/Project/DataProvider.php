<?php
/**
 * Project DataProvider
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\DataProvider\Project;

use TextMaster\TextMaster\Model\ResourceModel\Project\CollectionFactory;
use TextMaster\TextMaster\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use TextMaster\TextMaster\Model\Project;
use TextMaster\TextMaster\Helper\TranslatableContent as TranslatableContentHelper;

class DataProvider extends AbstractDataProvider
{
    /** @var TranslatableContentHelper */
    protected $translatableContentHelper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $projectFactory
     * @param DataPersistorInterface $dataPersistor
     * @param TranslatableContentHelper $translatableContentHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $projectFactory,
        DataPersistorInterface $dataPersistor,
        TranslatableContentHelper $translatableContentHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $projectFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $dataPersistor,
            Project::CACHE_TAG,
            $meta,
            $data
        );
        $this->translatableContentHelper = $translatableContentHelper;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = parent::getData();
        if (empty($data)) {
            $data[null] = $this->translatableContentHelper->getSelectByDefaultAttributes();
        }
        return $data;
    }
}
