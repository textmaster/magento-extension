<?php
/**
 * Callback Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\CallbackResponseInterface;

/**
 * @api
 */
interface CallbackInterface
{
    /**
     * @param string $token
     * @param string $callback
     * @param string $id
     * @param string $status
     * @return CallbackResponseInterface|null
     * @since 101.0.0
     */
    public function updateProjectStatus(
        string $token,
        string $callback,
        string $id,
        string $status
    );

    /**
     * @param string $token
     * @param string $callback
     * @param string $id
     * @param string $status
     * @return CallbackResponseInterface|null
     * @since 101.0.0
     */
    public function updateDocumentStatus(
        string $token,
        string $callback,
        string $id,
        string $status
    );
}
