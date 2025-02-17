<?php
/**
 * Custom Handler Logger
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

namespace TextMaster\TextMaster\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Custom extends Base
{
    /** @var string */
    protected $fileName = '/var/log/textmaster.log';

    /** @var int */
    protected $loggerType = Logger::DEBUG;
}
