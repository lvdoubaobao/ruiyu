<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Core file uploader model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Mazentop\AdvancedReview\Model;

class Uploader extends \Magento\Framework\File\Uploader
{

    public function __construct($fileId) {
        parent::__construct($fileId);
    }
    public function setUploadFileId($fileId)
    {
        if (is_array($fileId)) {
            $this->_uploadType = self::MULTIPLE_STYLE;
            $this->_file = $fileId;
        } else {
            if (empty($_FILES)) {
                throw new \Exception('$_FILES array is empty');
            }
            preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);
            if (count($file) > 0 && count($file[0]) > 0 && count($file[1]) > 0) {
                array_shift($file);
                $this->_uploadType = self::MULTIPLE_STYLE;
                $fileAttributes = $_FILES[$file[0]];
                $tmpVar = [];
                foreach ($fileAttributes as $attributeName => $attributeValue) {
                    $tmpVar[$attributeName] = $attributeValue[$file[1]];
                }
                $fileAttributes = $tmpVar;
                $this->_file = $fileAttributes;
            } elseif (count($fileId) > 0 && isset($_FILES[$fileId])) {
                $this->_uploadType = self::SINGLE_STYLE;
                $this->_file = $_FILES[$fileId];
            } elseif ($fileId == '') {
                throw new \Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
            }
        }
        if (!file_exists($this->_file['tmp_name'])) {
            $code = empty($this->_file['tmp_name']) ? self::TMP_NAME_EMPTY : 0;
            throw new \Exception('The file was not uploaded.', $code);
        } else {
            $this->_fileExists = true;
        }
    }

}
