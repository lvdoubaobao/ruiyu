<?php


namespace Niubi\Form\Model;


class VideoForm extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'niubi_video_form_post';

    protected $_cacheTag = 'niubi_video_form_post';
    protected $_idFieldName = 'form_id';
    protected $_eventPrefix = 'niubi_video_form_post';
    protected function _construct()
    {
        $this->_init('Niubi\Form\Model\ResourceModel\VideoForm');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}