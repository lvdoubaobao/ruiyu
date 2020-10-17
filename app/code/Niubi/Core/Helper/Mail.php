<?php


namespace Niubi\Core\Helper;

use Mailgun\Mailgun;
use mysql_xdevapi\Exception;
use Niubi\Core\Logger\Logger;

class Mail
{
    protected $helperData;
    protected  $logger;
    public function __construct(
        Data $helperData,
        Logger $logger
    )
    {
        $this->helperData = $helperData;
        $this->logger=$logger;
    }
    public function sendMail($subject,$desc,$form='',$to='',$to_name='',$tag=[]){
        $enable= $this->helperData->getGeneralConfig('enable');
        if (!$enable){
            $this->logger->info('success',[$to_name]);
            return false;
        }
        $mailgun_domian=$this->helperData->getGeneralConfig('mailgun_domian');
        $mailgun_key=$this->helperData->getGeneralConfig('mailgun_key');
        $mailgun_from=$this->helperData->getGeneralConfig('mailgun_from');
        $mailgun_from_name=$this->helperData->getGeneralConfig('mailgun_from_name');
        $mailgun_from=$mailgun_from_name." <".$mailgun_from.">";
        $mailgun_to=$this->helperData->getGeneralConfig('mailgun_to');
        $mailgun = Mailgun::create($mailgun_key);
        $domain = $mailgun_domian;
        $desc=str_replace('{{name}}',$to_name,$desc);
        $params = [
            'from' =>$form=='' ?  $mailgun_from  : $form,
         //     'to' => 'q736400469@gmail.com' ,
            'to' => $to=='' ? $mailgun_to : $to ,
            'subject' => $subject,
            'html' => $desc,
            'o:tag'   => $tag
        ];
        var_dump($subject);
        var_dump($form=='' ?  $mailgun_from  : $form);
        var_dump($desc);
        try{
            $result = $mailgun->messages()->send($domain, $params);
            var_dump($result);
            $this->logger->info('success',[$to]);
        }catch (\Mailgun\Exception\HttpClientException $exception){
            var_dump(111);
            $this->logger->error('Error message',['exception'=>$exception]);
        }

    }
}