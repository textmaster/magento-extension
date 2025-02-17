<?php
/**
 * TextMaster Connector helper
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Helper;

use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Model\Connector\GetLanguages;
use TextMaster\TextMaster\Model\Connector\GetCategories;
use TextMaster\TextMaster\Model\Connector\GetCapabilities;
use TextMaster\TextMaster\Model\Connector\GetTemplates;
use TextMaster\TextMaster\Model\Connector\GetDocument;
use TextMaster\TextMaster\Model\Connector\GetProject;
use TextMaster\TextMaster\Model\Connector\GetQuote;
use TextMaster\TextMaster\Model\Connector\CreateProject;
use TextMaster\TextMaster\Model\Connector\CreateDocument;
use TextMaster\TextMaster\Model\Connector\AcceptQuote;
use TextMaster\TextMaster\Model\Connector\AcceptDocument;
use TextMaster\TextMaster\Model\Connector\GetInformation;
use TextMaster\TextMaster\Model\Connector\AbstractService;
use TextMaster\TextMaster\Model\Connector\Analysis;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Connector
{
    /**
     * @var GetLanguages
     */
    protected $serviceGetLanguages;

    /**
     * @var GetCategories
     */
    protected $serviceGetCategories;

    /**
     * @var GetCapabilities
     */
    protected $serviceGetCapabilities;

    /**
     * @var GetTemplates
     */
    protected $serviceGetTemplates;

    /**
     * @var GetProject
     */
    protected $serviceGetProject;

    /**
     * @var GetDocument
     */
    protected $serviceGetDocument;

    /**
     * @var GetQuote
     */
    protected $serviceGetQuote;

    /**
     * @var CreateProject
     */
    protected $serviceCreateProject;

    /**
     * @var CreateDocument
     */
    protected $serviceCreateDocument;

    /**
     * @var AcceptQuote
     */
    protected $serviceAcceptQuote;

    /**
     * @var AcceptDocument
     */
    protected $serviceAcceptDocument;

    /**
     * @var GetInformation
     */
    protected $serviceGetInformation;

    /**
     * @var Analysis
     */
    protected $serviceAnalysis;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LoggerInterface
     */
    protected $textmasterLogger;

    /**
     * Connector constructor.
     * @param GetLanguages $serviceGetLanguages
     * @param GetCategories $serviceGetCategories
     * @param GetCapabilities $serviceGetCapabilities
     * @param GetTemplates $serviceGetTemplates
     * @param GetProject $serviceGetProject
     * @param GetDocument $serviceGetDocument
     * @param GetQuote $serviceGetQuote
     * @param CreateProject $serviceCreateProject
     * @param CreateDocument $serviceCreateDocument
     * @param AcceptQuote $serviceAcceptQuote
     * @param AcceptDocument $serviceAcceptDocument
     * @param GetInformation $serviceGetInformation
     * @param Analysis $serviceAnalysis
     * @param LoggerInterface $logger
     * @param LoggerInterface $textmasterLogger
     */
    public function __construct(
        GetLanguages $serviceGetLanguages,
        GetCategories $serviceGetCategories,
        GetCapabilities $serviceGetCapabilities,
        GetTemplates $serviceGetTemplates,
        GetProject $serviceGetProject,
        GetDocument $serviceGetDocument,
        GetQuote $serviceGetQuote,
        CreateProject $serviceCreateProject,
        CreateDocument $serviceCreateDocument,
        AcceptQuote $serviceAcceptQuote,
        AcceptDocument $serviceAcceptDocument,
        GetInformation $serviceGetInformation,
        Analysis $serviceAnalysis,
        LoggerInterface $logger,
        LoggerInterface $textmasterLogger
    ) {
        $this->serviceGetLanguages = $serviceGetLanguages;
        $this->serviceGetCategories = $serviceGetCategories;
        $this->serviceGetCapabilities = $serviceGetCapabilities;
        $this->serviceGetTemplates = $serviceGetTemplates;
        $this->serviceGetProject = $serviceGetProject;
        $this->serviceGetDocument = $serviceGetDocument;
        $this->serviceGetQuote = $serviceGetQuote;
        $this->serviceCreateProject = $serviceCreateProject;
        $this->serviceCreateDocument = $serviceCreateDocument;
        $this->serviceAcceptQuote = $serviceAcceptQuote;
        $this->serviceAcceptDocument = $serviceAcceptDocument;
        $this->serviceGetInformation = $serviceGetInformation;
        $this->serviceAnalysis = $serviceAnalysis;
        $this->logger = $logger;
        $this->textmasterLogger = $textmasterLogger;
    }

    /**
     * @param AbstractService $service
     *
     * @return array
     * @throws LocalizedException
     */
    protected function executeService(AbstractService $service)
    {
        $response = [];
        try {
            $service->execute();
            $response = $service->getResponseBody();
        } catch (Exception $exception) {
            $this->logServiceException(
                $exception,
                $service
            );
        }
        return $response;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategories(): array
    {
        return $this->executeService($this->serviceGetCategories);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getLanguages(): array
    {
        return $this->executeService($this->serviceGetLanguages);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCapabilities(): array
    {
        return $this->executeService($this->serviceGetCapabilities);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTemplates(): array
    {
        return $this->executeService($this->serviceGetTemplates);
    }

    /**
     * @param string $projectTextmasterId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getProjects(string $projectTextmasterId): array
    {
        $this->serviceGetProject->setProjectId($projectTextmasterId);
        return $this->executeService($this->serviceGetProject);
    }

    /**
     * @param string $projectTextmasterId
     * @param string $documentTextmasterId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getDocuments(string $projectTextmasterId, string $documentTextmasterId): array
    {
        $this->serviceGetDocument->setProjectId($projectTextmasterId);
        $this->serviceGetDocument->setDocumentId($documentTextmasterId);
        return $this->executeService($this->serviceGetDocument);
    }

    /**
     * @param string $projectTextmasterId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getQuote(string $projectTextmasterId): array
    {
        $this->serviceGetQuote->setProjectId($projectTextmasterId);
        return $this->executeService($this->serviceGetQuote);
    }

    /**
     * @param ProjectInterface $project
     *
     * @return array
     * @throws LocalizedException
     */
    public function createProject(ProjectInterface $project): array
    {
        $this->serviceCreateProject->setProjectData($project);
        return $this->executeService($this->serviceCreateProject);
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param array $projectAttributesCode
     *
     * @return array
     * @throws LocalizedException
     */
    public function createDocument(
        ProjectInterface $project,
        DocumentInterface $document,
        array $projectAttributesCode
    ): array {
        $this->serviceCreateDocument->setProjectId($project->getTextMasterId());
        $isDocumentDatas = $this->serviceCreateDocument->setDocumentData($project, $document, $projectAttributesCode);
        if ($isDocumentDatas) {
            return $this->executeService($this->serviceCreateDocument);
        }
        return [];
    }

    /**
     * @param string $tmsProjectId
     * @param string $status
     *
     * @return array
     * @throws LocalizedException
     */
    public function acceptQuote(string $tmsProjectId, string $status): array
    {
        $this->serviceAcceptQuote->setProjectId($tmsProjectId);
        $this->serviceAcceptQuote->setQuoteStatus($status);
        return $this->executeService($this->serviceAcceptQuote);
    }

    /**
     * @param string $tmsProjectId
     * @param string $tmsDocumentId
     * @param string $status
     *
     * @return array
     * @throws LocalizedException
     */
    public function acceptDocument(string $tmsProjectId, string $tmsDocumentId, string $status): array
    {
        $this->serviceAcceptDocument->setProjectId($tmsProjectId);
        $this->serviceAcceptDocument->setDocumentId($tmsDocumentId);
        $this->serviceAcceptDocument->setDocumentStatus($status);
        return $this->executeService($this->serviceAcceptDocument);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getInformation(): array
    {
        return $this->executeService($this->serviceGetInformation);
    }

    /**
     * @param string $tmsProjectId
     *
     * @return array
     * @throws LocalizedException
     */
    public function analysis(string $tmsProjectId): array
    {
        $this->serviceAnalysis->setProjectId($tmsProjectId);
        return $this->executeService($this->serviceAnalysis);
    }

    /**
     * Log service problems and throw a service exception
     *
     * @param Exception            $exception
     * @param AbstractService|null $service
     *
     * @return void
     * @throws LocalizedException
     */
    protected function logServiceException(Exception $exception, AbstractService $service = null)
    {
        $this->logger->critical('Error TextMaster API Services', ['exception' => $exception]);
        if (isset($service)) {
            $this->textmasterLogger->critical($service->getErrorMessages());
        }
        throw new LocalizedException(
            __('An error occurred while connecting to API TextMaster')
        );
    }
}
