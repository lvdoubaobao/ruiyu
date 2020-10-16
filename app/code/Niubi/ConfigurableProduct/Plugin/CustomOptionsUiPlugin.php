<?php


namespace Niubi\ConfigurableProduct\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\DataType\Number;
/**
 * Data provider for "Customizable Options" panel
 */
class CustomOptionsUiPlugin
{
    /**
     * Field values
     */
    const FIELD_IS_DEFAULT = 'stock';

    /**
     * @param \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject
     * @param bool $meta
     * @return bool
     */
    public function afterModifyMeta(CustomOptions $subject, $meta)
    {

        $result = $meta;


        $result[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
        [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
        [CustomOptions::CONTAINER_OPTION]['children']
        [CustomOptions::GRID_TYPE_SELECT_NAME]['children']['record']['children']
        [static::FIELD_IS_DEFAULT] = $this->getIsDefaultFieldConfig(51);
        /*    var_dump($result[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
            [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
            [CustomOptions::CONTAINER_OPTION]['children']
            [CustomOptions::GRID_TYPE_SELECT_NAME]['children']['record']['children']);*/
        //    exit();
        return $result;

    }

    /**
     * Get config for checkbox field used for default values
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getIsDefaultFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Is out of stock'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_IS_DEFAULT,
                        'dataType' => Text::NAME,

                        'sortOrder' => $sortOrder,

                    ],
                ],
            ],
        ];
    }
}