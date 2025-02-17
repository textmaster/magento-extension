<?php
/**
 * Callback Response Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface CallbackResponseInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_MESSAGE = 'message';
    /**#@-*/

    /**
     * Get field: message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set field: message
     *
     * @param string $value
     *
     * @return CallbackResponseInterface
     */
    public function setMessage(string $value): CallbackResponseInterface;
}
