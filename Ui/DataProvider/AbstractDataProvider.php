<?php
/**
 * AbstractDataProvider
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\DataProvider;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Ui\DataProvider\AbstractDataProvider as MagentoAbstractDataProvider;
use Psr\Log\NullLogger;

abstract class AbstractDataProvider extends MagentoAbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var string
     */
    protected $dataPersistorKey;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param string                 $dataPersistorKey
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        DataPersistorInterface $dataPersistor,
        string $dataPersistorKey,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->dataPersistor    = $dataPersistor;
        $this->dataPersistorKey = $dataPersistorKey;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        $items = $this->collection->getItems();
        /** @var AbstractModel $model */
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
        }

        $data = $this->dataPersistor->get($this->dataPersistorKey);
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear($this->dataPersistorKey);
        }

        return $this->loadedData;
    }
}
