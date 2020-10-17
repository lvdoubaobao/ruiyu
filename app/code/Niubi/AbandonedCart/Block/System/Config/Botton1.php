<?php


namespace Niubi\AbandonedCart\Block\System\Config;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Botton1 extends  Field
{
    protected $_template = 'Niubi_AbandonedCart::system/config/button.phtml';
    protected  $_data;
    public function __construct(
        Context $context,
        array $data = []
    ) {
     //   $this->_data=$data;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    protected function _getElementHtml(AbstractElement $element)
    {

        return $this->_toHtml();
    }
    public function getCustomUrl()
    {
        return $this->getUrl('abandonedcart/email/Review',['tel'=>'tel1']);
    }
    public function getButtonHtml()
    {

        //exit();
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'btn_id',
                'label' => __('Review'),
            ]
        );
        return $button->toHtml();
    }
}