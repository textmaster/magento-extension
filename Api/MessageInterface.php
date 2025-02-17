<?php
/**
 * Message Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

interface MessageInterface
{
    /**
     * @param int $documentId document ID
     *
     * @return MessageInterface
     */
    public function setDocumentId(int $documentId);

    /**
     * @return int
     */
    public function getDocumentId();

    /**
     * @param int $projectId project ID
     *
     * @return MessageInterface
     */
    public function setProjectId(int $projectId);

    /**
     * @return int
     */
    public function getProjectId();
}
