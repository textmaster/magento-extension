<?php
/**
 * Block Grid Action
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use TextMaster\TextMaster\Block\Adminhtml\Project\Tab\Block;

class BlockGrid extends AbstractAction
{
    /**
     * @return Raw
     */
    public function execute()
    {
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                Block::class,
                'project.block.grid'
            )->toHtml()
        );
    }
}
