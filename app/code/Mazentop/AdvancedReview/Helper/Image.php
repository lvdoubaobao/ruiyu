<?php

namespace Mazentop\AdvancedReview\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Blog image helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Image extends AbstractHelper
{
    protected $_quality = 60;
    /**
     * Custom directory relative to the "media" folder
     */
    const DIRECTORY = 'review/img/';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_backgroundColor = [255, 255, 255];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize image
     * @return string
     */
    public function resize($image, $width = null, $height = null)
    {

        $image = str_replace('pub/media/review/img/', '', $image);
        $mediaFolder = self::DIRECTORY;
        $absolutePath = $this->_mediaDirectory->getAbsolutePath($mediaFolder) . $image;
        //todo 这里判断图片是否存在
        if ( file_exists($absolutePath)) {
            $imageinfo = getimagesize($absolutePath);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            //TODO resize width and height 1000
            if ($width > 300) {
                $newWidth = 300;
                $height = intval($newWidth * $height / $width);
                $width = $newWidth;
            }
            $path = $mediaFolder . 'cache';
            if ($width !== null) {
                $path .= '/' . $width . 'x';
                if ($height !== null) {
                    $path .= $height;
                }
            }
            $imageResized = $this->_mediaDirectory->getAbsolutePath($path) . $image;
            try {
                if (!$this->_fileExists($path . $image)) {
                    $imageFactory = $this->_imageFactory->create();
                    $imageFactory->open($absolutePath);
                    $imageFactory->constrainOnly(true);
                    $imageFactory->keepTransparency(true);
                    $imageFactory->keepFrame(true);
                    $imageFactory->keepAspectRatio(true);
                    $imageFactory->backgroundColor($this->_backgroundColor);
                    $imageFactory->quality($this->_quality);
                    $imageFactory->resize($width, $height);
                    $imageFactory->save($imageResized);
                }
                return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . $image;
            } catch (\Exception $e) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info(var_export($e->getMessage(), true));
            }
            return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'review/img/' . $image;

        }else{
            return '';
        }
    }
}
