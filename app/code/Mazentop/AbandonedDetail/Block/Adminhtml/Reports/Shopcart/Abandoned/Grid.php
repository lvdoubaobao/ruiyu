<?php
namespace Mazentop\AbandonedDetail\Block\Adminhtml\Reports\Shopcart\Abandoned;

class Grid extends \Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid {
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getQuoteEntityId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => 'abandonedetail/view/index',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'quote_entity_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
    }
}