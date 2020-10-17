<?php


namespace Niubi\AbandonedCart\Logger;


use Magento\Framework\Filesystem\DriverInterface;

class Handler  extends   \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = 'AbandonedCart.log';
    public function __construct(DriverInterface $filesystem, $filePath = null)
    {
        $filePath=BP . DIRECTORY_SEPARATOR.'var/log'.DIRECTORY_SEPARATOR.date('Y-m', time()).
            DIRECTORY_SEPARATOR.date("d").DIRECTORY_SEPARATOR;
        parent::__construct($filesystem, $filePath);
    }
}