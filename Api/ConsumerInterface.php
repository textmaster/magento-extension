<?php
/**
 * Consumer Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

interface ConsumerInterface
{
    public const TOPIC_TEXTMASTER_APPLY_TRANSLATION = 'textmaster.apply_translation';

    /**
     * @param MessageInterface $message message
     *
     * @return void
     */
    public function processMessage(MessageInterface $message);
}
