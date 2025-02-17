<?php
/**
 * Block in project grid
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml\Project\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Block extends Extended
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @param Context               $context
     * @param Data                  $backendHelper
     * @param BlockFactory        $blockFactory
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialization
     *
     * @return void
     * @throws FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('textmaster_project_blocks_grid');
        $this->setDefaultSort('block_id');
        $this->setUseAjax(true);
    }

    /**
     * Add column filtering conditions to collection
     *
     * @param Column $column
     *
     * @return Block
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column): Block
    {
        // Set custom filter for in project flag
        if ($column->getId() == 'in_project') {
            $blockIds = $this->getSelectedBlocks();
            if (empty($blockIds)) {
                $blockIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('block_id', ['in' => $blockIds]);
            } else {
                if ($blockIds) {
                    $this->getCollection()->addFieldToFilter('block_id', ['nin' => $blockIds]);
                }
            }
        } else {
            \Magento\Backend\Block\Widget\Grid::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return Block
     */
    protected function _prepareCollection(): Block
    {
        $collection = $this->blockFactory->create()->getCollection()->addFieldToSelect(
            'block_id'
        )->addFieldToSelect(
            'title'
        )->addFieldToSelect(
            'identifier'
        )->addFieldToSelect(
            'is_active'
        );

        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Initialize grid columns
     *
     * @return Block
     * @throws Exception
     */
    protected function _prepareColumns(): Block
    {
        $this->addColumn(
            'in_project',
            [
                'type' => 'checkbox',
                'name' => 'in_project',
                'required' => true,
                'field_name' => $this->getId() . '[]',
                'values' => $this->getSelectedBlocks(),
                'index' => 'block_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'block_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'block_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title col-document-name'
            ]
        );
        $this->addColumn(
            'identifier',
            [
                'header' => __('URL Key'),
                'index' => 'identifier',
                'header_css_class' => 'col-identifier',
                'column_css_class' => 'col-identifier'
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->blockFactory->create()->getAvailableStatuses(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('*/project/blockGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelectedBlocks(): array
    {
        $blocks = $this->getRequest()->getPost('selected_documents');
        if ($blocks === null) {
            return [];
        }
        return $blocks;
    }

    /**
     * @return string
     */
    public function getInputName():string
    {
        return 'block_ids';
    }

    /**
     * @return string
     */
    public function getInputDataFormPart():string
    {
        return 'textmaster_project_form';
    }

    /**
     * @return string
     */
    public function getInputId():string
    {
        return 'in_textmaster_project_blocks';
    }

    /**
     * @return string
     */
    public function getErrorId():string
    {
        return 'textmaster_project_blocks_error';
    }

    /**
     * @return Phrase
     */
    public function getErrorMessage(): Phrase
    {
        return __('Block requires');
    }
}
