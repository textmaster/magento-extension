<?php
/**
 * Page in project grid
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
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Page extends Extended
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @param Context               $context
     * @param Data                  $backendHelper
     * @param PageFactory        $pageFactory
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        PageFactory $pageFactory,
        array $data = []
    ) {
        $this->pageFactory = $pageFactory;
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
        $this->setId('textmaster_project_pages_grid');
        $this->setDefaultSort('page_id');
        $this->setUseAjax(true);
    }

    /**
     * Add column filtering conditions to collection
     *
     * @param Column $column
     *
     * @return Page
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column): Page
    {
        // Set custom filter for in project flag
        if ($column->getId() == 'in_project') {
            $pageIds = $this->getSelectedPages();
            if (empty($pageIds)) {
                $pageIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('page_id', ['in' => $pageIds]);
            } else {
                if ($pageIds) {
                    $this->getCollection()->addFieldToFilter('page_id', ['nin' => $pageIds]);
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
     * @return Page
     */
    protected function _prepareCollection(): Page
    {
        $collection = $this->pageFactory->create()->getCollection()->addFieldToSelect(
            'page_id'
        )->addFieldToSelect(
            'title'
        )->addFieldToSelect(
            'identifier'
        )->addFieldToSelect(
            'page_layout'
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
     * @return Page
     * @throws Exception
     */
    protected function _prepareColumns(): Page
    {
        $this->addColumn(
            'in_project',
            [
                'type' => 'checkbox',
                'name' => 'in_project',
                'required' => true,
                'field_name' => $this->getId() . '[]',
                'values' => $this->getSelectedPages(),
                'index' => 'page_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'page_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'page_id',
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
            'page_layout',
            [
                'header' => __('Layout'),
                'index' => 'page_layout',
                'header_css_class' => 'col-page-layout',
                'column_css_class' => 'col-page-layout'
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->pageFactory->create()->getAvailableStatuses(),
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
        return $this->getUrl('*/project/pageGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelectedPages(): array
    {
        $pages = $this->getRequest()->getPost('selected_documents');
        if ($pages === null) {
            return [];
        }
        return $pages;
    }

    /**
     * @return string
     */
    public function getInputName():string
    {
        return 'page_ids';
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
        return 'in_textmaster_project_pages';
    }

    /**
     * @return string
     */
    public function getErrorId():string
    {
        return 'textmaster_project_pages_error';
    }

    /**
     * @return Phrase
     */
    public function getErrorMessage(): Phrase
    {
        return __('Page requires');
    }
}
