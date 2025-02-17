<?php
/**
 * Callback Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\CallbackInterface;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface as ProjectRepository;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface as DocumentRepository;
use TextMaster\TextMaster\Helper\Connector;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use TextMaster\TextMaster\Api\Data\CallbackResponseInterfaceFactory;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;

class Callback implements CallbackInterface
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var DocumentRepository
     */
    protected $documentRepository;

    /**
     * @var Connector
     */
    protected $connectorHelper;

    /**
     * @var CallbackResponseInterfaceFactory
     */
    protected $callbackResponseFactory;

    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * Callback constructor.
     * @param ProjectRepository $projectRepository
     * @param DocumentRepository $documentRepository
     * @param Connector $connectorHelper
     * @param CallbackResponseInterfaceFactory $callbackResponseFactory
     * @param ProjectHelper $projectHelper
     */
    public function __construct(
        ProjectRepository $projectRepository,
        DocumentRepository $documentRepository,
        Connector $connectorHelper,
        CallbackResponseInterfaceFactory $callbackResponseFactory,
        ProjectHelper $projectHelper
    ) {
        $this->projectRepository = $projectRepository;
        $this->documentRepository = $documentRepository;
        $this->connectorHelper = $connectorHelper;
        $this->callbackResponseFactory = $callbackResponseFactory;
        $this->projectHelper = $projectHelper;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function updateProjectStatus(
        string $token,
        string $callback,
        string $id,
        string $status
    ) {
        $project = $this->projectRepository->getByTextMasterId($id);
        $response = $this->callbackResponseFactory->create();

        if ($token === $project->getToken()) {
            $update = false;
            if ($callback === 'project_finalized' && !$project->getPrice()) {
                $quote = $this->connectorHelper->getQuote($project->getTextMasterId());
                if (isset($quote['price']) && isset($quote['currency'])) {
                    $project->setPrice($quote['price']);
                    $project->setCurrency($quote['currency']);
                    $update = true;
                }
            }
            if ($status !== $project->getStatus()) {
                $project->setStatus($status);
                $update = true;
            }
            if ($update === false) {
                $response->setMessage('Nothing to update');
                return $response;
            }
            try {
                $this->projectRepository->save($project);
                $response->setMessage('Project has been updated');
            } catch (LocalizedException $e) {
                $response->setMessage('Something went wrong when saving');
            }
        }
        return $response;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function updateDocumentStatus(
        string $token,
        string $callback,
        string $id,
        string $status
    ) {
        $document = $this->documentRepository->getByTextMasterId($id);
        $response = $this->callbackResponseFactory->create();

        if ($token === $document->getToken() && $status !== $document->getStatus()) {
            $document->setStatus($status);
            try {
                $this->documentRepository->save($document);
                $response->setMessage('Document has been updated');
                $this->projectHelper->updateProjectStatus($document);
            } catch (LocalizedException $e) {
                $response->setMessage('Something went wrong when saving');
            }
        }
        return $response;
    }
}
