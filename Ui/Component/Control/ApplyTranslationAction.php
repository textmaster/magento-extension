<?php
/**
 * Class ApplyTranslationAction
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\Component\Control;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Control\Action;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;

class ApplyTranslationAction extends Action
{
    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * ApplyTranslationAction constructor.
     * @param ContextInterface $context
     * @param ProjectHelper $projectHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ProjectHelper $projectHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->projectHelper = $projectHelper;
    }

    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $projectId = (int)$context->getRequestParam('project_id');

        if ($this->projectHelper->isBeingTranslatedProject($this->projectHelper->getProjectById($projectId))) {
            $config['url'] = '';
        } else {
            $config['url'] = $context->getUrl(
                '*/*/applyTranslation',
                ['project_id' => $projectId]
            );
        }

        $this->setData('config', (array)$config);
        parent::prepare();
    }
}
