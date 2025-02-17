<?php
/**
 * Class ProjectLink
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\Component\Listing\Column;

use TextMaster\TextMaster\Helper\Data as TextMasterHelper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProjectLink extends Column
{
    /**
     * @var TextMasterHelper
     */
    protected $textmasterHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TextMasterHelper $textmasterHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TextMasterHelper $textmasterHelper,
        array $components = [],
        array $data = []
    ) {
        $this->textmasterHelper = $textmasterHelper;
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
                if (isset($item['textmaster_id'])) {
                    $item[$this->getData('name')] = [
                        'textmaster_link' => [
                            'href' => $this->getProjectPageUrl($item['textmaster_id']),
                            'label' => $item['textmaster_id'],
                            'target' => '_blank',
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param string $textMasterId
     *
     * @return string
     */
    protected function getProjectPageUrl(string $textMasterId): string
    {
        return sprintf($this->textmasterHelper->getProjectPageUrl(), $textMasterId);
    }
}
