<?php
namespace Mazentop\AbandonedDetail\Model\ResourceModel\Quote;
//use Magento\Reports\Model\ResourceModel\Quote\Collection;
class CollectionPlugin {
    public function beforeResolveCustomerNames($object){
        foreach ($object->getItems() as $item) {
            $item->setData('quote_entity_id',$item->getData('entity_id'));
        }
    }
}