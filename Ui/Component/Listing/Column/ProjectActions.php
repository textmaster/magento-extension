<?php
/**
 * Class ProjectActions
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Escaper;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;

class ProjectActions extends Column
{
    /** Url path */
    const URL_PATH_VIEW   = 'textmaster/project/view';
    const URL_PATH_DELETE = 'textmaster/project/delete';
    const URL_PATH_VALIDATE_QUOTE = 'textmaster/project/validateQuote';
    const URL_PATH_APPLY_TRANSLATION = 'textmaster/project/applyTranslation';

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var Escaper */
    protected $escaper;

    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * ProjectActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param ConnectorHelper $connectorHelper
     * @param ProjectHelper $projectHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        ConnectorHelper $connectorHelper,
        ProjectHelper $projectHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper    = $escaper;
        $this->connectorHelper = $connectorHelper;
        $this->projectHelper = $projectHelper;

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
                if (isset($item[ProjectInterface::FIELD_PROJECT_ID])) {
                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_VIEW,
                                [ProjectInterface::FIELD_PROJECT_ID => $item[ProjectInterface::FIELD_PROJECT_ID]]
                            ),
                            'label' => __('View detail')
                        ]
                    ];
                }
                if (isset(
                    $item[ProjectInterface::FIELD_STATUS],
                    $item[ProjectInterface::FIELD_PROJECT_ID],
                    $item[ProjectInterface::FIELD_AUTOLAUNCH],
                    $item[ProjectInterface::FIELD_QUOTE_VALIDATED]
                ) && (
                        $item[ProjectInterface::FIELD_STATUS] === ProjectInterface::STATUS_IN_CREATION &&
                        !empty($item[ProjectInterface::FIELD_PRICE])
                    ) &&
                    (bool)$item[ProjectInterface::FIELD_AUTOLAUNCH] === false &&
                    (bool)$item[ProjectInterface::FIELD_QUOTE_VALIDATED] === false
                ) {
                    $item[$this->getData('name')]['validate_quote'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_VALIDATE_QUOTE,
                            [ProjectInterface::FIELD_PROJECT_ID => $item[ProjectInterface::FIELD_PROJECT_ID]]
                        ),
                        'label' => __('Validate Quote')
                    ];
                }
                $projectId = (int)$item[ProjectInterface::FIELD_PROJECT_ID];
                if (isset($item[ProjectInterface::FIELD_STATUS], $item[ProjectInterface::FIELD_PROJECT_ID]) &&
                    ($item[ProjectInterface::FIELD_STATUS] === ProjectInterface::STATUS_IN_REVIEW) &&
                    !$this->projectHelper->isBeingTranslatedProject($this->projectHelper->getProjectById($projectId))
                ) {
                    $item[$this->getData('name')]['apply_translation'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_APPLY_TRANSLATION,
                            [ProjectInterface::FIELD_PROJECT_ID => $item[ProjectInterface::FIELD_PROJECT_ID]]
                        ),
                        'label' => __('Apply Translation')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
