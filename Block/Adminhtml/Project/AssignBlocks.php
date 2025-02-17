<?php
/**
 * Assign Blocks Block
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml\Project;

use TextMaster\TextMaster\Block\Adminhtml\Project\Tab\Block;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\BlockInterface;

class AssignBlocks extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'TextMaster_TextMaster::project/form/assign_documents.phtml';

    /**
     * @var Block
     */
    protected $blockGrid;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var CollectionFactory
     */
    protected $blockFactory;

    /**
     * @param Context           $context
     * @param Registry          $registry
     * @param EncoderInterface  $jsonEncoder
     * @param CollectionFactory $blockFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EncoderInterface $jsonEncoder,
        CollectionFactory $blockFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid(): BlockInterface
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Block::class,
                'project.block.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws LocalizedException
     */
    public function getGridHtml(): string
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getDocumentsJson(): string
    {
        return '{}';
    }

    /**
     * Retrieve current project instance
     *
     * @return array|null
     */
    public function getProject(): ?array
    {
        return $this->registry->registry('current_project');
    }
}
