<?php
/**
 * Class DocumentLink
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\Component\Listing\Column;

use TextMaster\TextMaster\Api\ProjectRepositoryInterface;
use TextMaster\TextMaster\Helper\Data as TextMasterHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class DocumentLink extends Column
{
    /**
     * @var TextMasterHelper
     */
    protected $textmasterHelper;

    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;

    /**
     * @var string
     */
    protected $projectTextmasterId;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TextMasterHelper $textmasterHelper
     * @param ProjectRepositoryInterface $projectRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TextMasterHelper $textmasterHelper,
        ProjectRepositoryInterface $projectRepository,
        array $components = [],
        array $data = []
    ) {
        $this->textmasterHelper = $textmasterHelper;
        $this->projectRepository = $projectRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['textmaster_id'])) {
                    $projectTextmasterId = $this->getProjectTextmasterId((int) $item['project_id']);
                    $item[$this->getData('name')] = [
                        'textmaster_link' => [
                            'href' => $this->getDocumentPageUrl($projectTextmasterId, $item['textmaster_id']),
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
     * @param string $projectTextMasterId
     * @param string $documentTextMasterId
     *
     * @return string
     */
    protected function getDocumentPageUrl(string $projectTextMasterId, string $documentTextMasterId): string
    {
        return sprintf($this->textmasterHelper->getDocumentPageUrl(), $projectTextMasterId, $documentTextMasterId);
    }

    /**
     * @param int $projectId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProjectTextmasterId(int $projectId): string
    {
        if ($this->projectTextmasterId === null) {
            $project = $this->projectRepository->getById($projectId);
            $this->projectTextmasterId = $project->getTextMasterId();
        }
        return $this->projectTextmasterId;
    }
}
