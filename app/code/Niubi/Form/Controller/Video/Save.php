<?php


namespace Niubi\Form\Controller\Video;


use Niubi\Core\Helper\Mail;
use Niubi\Form\Model\VideoForm;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $_videoFormFactory;
    protected $mail;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Niubi\Form\Model\VideoFormFactory $_videoFormFactory,
        Mail $mail

    )
    {
        $this->mail = $mail;
        $this->_pageFactory = $pageFactory;
        $this->_videoFormFactory = $_videoFormFactory;
        return parent::__construct($context);
    }

    public function execute()
    {

        $params = $this->getRequest()->getParams();
        $videoForm = $this->_videoFormFactory->create();
        if ($this->validateForm()) {
            $params['created_at']=date('Y-m-d H:i:s',time());
            $videoForm->addData($params);
            $saveData = $videoForm->save();
            $this->mail->sendMail('video_from', "
                name:" . $videoForm['name'] . "<br/>" .
                "email:" . $videoForm['email'] . "<br/>" .
                "type:" . $videoForm['type'] . "<br/>" .
                "link:" . $videoForm['link'] . "<br/>" .
                "content:" . $videoForm['content'] . "<br/>"
            );
            $this->messageManager->addSuccessMessage('Success');
            $this->_redirect($this->_redirect->getRefererUrl());
        } else {
            $this->messageManager->addErrorMessage('Fail');
            $this->_redirect($this->_redirect->getRefererUrl());

        }


    }

    protected function validateForm()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['name']) && $params['name'] != '' && isset($params['email']) && $params['email'] != '' && isset($params['link']) && $params['link'] != '') {
            return true;
        } else {
            return false;
        }
    }
}