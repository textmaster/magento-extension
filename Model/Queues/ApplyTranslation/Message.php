<?php
/**
 * Apply Translation Consumer Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Queues\ApplyTranslation;

use TextMaster\TextMaster\Api\MessageInterface;

class Message implements MessageInterface
{
    /**
     * @var int
     */
    protected $documentId;

    /**
     * @var int
     */
    protected $projectId;

    /**
     * @inheritDoc
     */
    public function setDocumentId(int $documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @inheritDoc
     */
    public function setProjectId(int $projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProjectId()
    {
        return $this->projectId;
    }
}
