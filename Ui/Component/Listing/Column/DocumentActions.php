<?php
/**
 * Class DocumentActions
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Ui\Component\Listing\Column;

use TextMaster\TextMaster\Api\Data\ProjectInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Escaper;

class DocumentActions extends Column
{
    /** Url path */
    const URL_PATH_APPLY_TRANSLATION   = 'textmaster/project/applyTranslation';

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var Escaper */
    protected $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper    = $escaper;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[ProjectInterface::FIELD_PROJECT_ID], $item[ProjectInterface::FIELD_STATUS]) &&
                    $item[ProjectInterface::FIELD_STATUS] === ProjectInterface::STATUS_IN_REVIEW
                ) {
                    $item[$this->getData('name')] = [
                        'applyTranslation' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_APPLY_TRANSLATION,
                                [ProjectInterface::FIELD_PROJECT_ID => $item[ProjectInterface::FIELD_PROJECT_ID]]
                            ),
                            'label' => __('Apply Translation')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
