<?php
/**
 * Class Create Project Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use TextMaster\TextMaster\Api\Data\ProjectInterface;
use Exception;

class CreateProject extends AbstractService
{
    const API_ENDPOINT = 'projects';

    const TEXTMASTER_PROJECT_CALLBACKS = [
        'project_in_progress',
        'project_finalized',
        'project_not_launched',
        'project_cancelled',
        'project_tm_completed',
        'project_tm_diff_completed'
    ];

    const MAGENTO_PROJECT_CALLBACK_URL = 'rest/V1/textmaster-textmaster/project/token/%s/callback/%s';

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = $this->getUrl();
        $this->curlClient->setHeaders($this->getHeaders(true));
        $this->curlClient->post($uri, $this->getParams());
        $this->checkResponse();
    }

    /**
     * Set data
     * since template is mandatory, we always use create project with template pattern
     * @param ProjectInterface $project
     */
    public function setProjectData(ProjectInterface $project)
    {
        $projectData = [];

        $projectData['name'] = $project->getName(); // required
        $projectData['templateId'] = $project->getTemplateId(); // required
        $projectData['notes'] = $project->getNotes();
        $category = $project->getCategory();
        if (empty($category)) {
            $category = null;
        }
        $projectData['options']['category'] = $category;
        $projectData['options']['languageLevel'] = $project->getLanguageLevel();
        $projectData['callbacks'] = $this->getCallbacks(
            $project->getToken(),
            self::TEXTMASTER_PROJECT_CALLBACKS,
            self::MAGENTO_PROJECT_CALLBACK_URL
        );

        $this->setParams(
            $this->serializer->serialize($projectData)
        );
    }

    /**
     * @return mixed
     */
    public function getEndpoint(): string
    {
        return self::API_ENDPOINT;
    }
}
