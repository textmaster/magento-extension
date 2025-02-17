<?php
/**
 * Product in project grid
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
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetsCollectionFactory;
use Magento\Framework\Phrase;

class Product extends Extended
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var SetsCollectionFactory
     */
    protected $setsCollectionFactory;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var Visibility
     */
    protected $visibility;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @param Context               $context
     * @param Data                  $backendHelper
     * @param ProductFactory        $productFactory
     * @param SetsCollectionFactory $setsCollectionFactory
     * @param Status                $status
     * @param Visibility            $visibility
     * @param Type                  $type
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ProductFactory $productFactory,
        SetsCollectionFactory $setsCollectionFactory,
        Status $status,
        Visibility $visibility,
        Type $type,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->setsCollectionFactory = $setsCollectionFactory;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->type = $type;
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
        $this->setId('textmaster_project_products_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * Add column filtering conditions to collection
     *
     * @param Column $column
     *
     * @return Product
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column): Product
    {
        // Set custom filter for in project flag
        if ($column->getId() == 'in_project') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
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
     * @return Product
     */
    protected function _prepareCollection(): Product
    {
        $collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'visibility'
        )->addAttributeToSelect(
            'type_id'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'status'
        )->addAttributeToSelect(
            'price'
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
     * @return Product
     * @throws Exception
     */
    protected function _prepareColumns(): Product
    {
        $this->addColumn(
            'in_project',
            [
                'type' => 'checkbox',
                'name' => 'in_project',
                'required' => true,
                'field_name' => $this->getId() . '[]',
                'values' => $this->getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name col-document-name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->type->getOptionArray()
            ]
        );

        $sets = $this->setsCollectionFactory->create()->setEntityTypeFilter(
            $this->productFactory->create()->getResource()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'attribute_set_id',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('*/project/productGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelectedProducts(): array
    {
        $products = $this->getRequest()->getPost('selected_documents');
        if ($products === null) {
            return [];
        }
        return $products;
    }

    /**
     * @return string
     */
    public function getInputName():string
    {
        return 'product_ids';
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
        return 'in_textmaster_project_products';
    }

    /**
     * @return string
     */
    public function getErrorId():string
    {
        return 'textmaster_project_products_error';
    }

    /**
     * @return Phrase
     */
    public function getErrorMessage(): Phrase
    {
        return __('Product requires');
    }
}
