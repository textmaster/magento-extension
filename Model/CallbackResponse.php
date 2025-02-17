<?php
/**
 * Callback Response Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\CallbackResponseInterface;
use Magento\Framework\Model\AbstractModel;

class CallbackResponse extends AbstractModel implements CallbackResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string) $this->getData(self::FIELD_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $value): CallbackResponseInterface
    {
        return $this->setData(self::FIELD_MESSAGE, $value);
    }
}
