<?php


namespace Niubi\AbandonedCart\Controller\test;


use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\ResourceModel\Quote;

use Magento\Setup\Exception;
use Niubi\AbandonedCart\Block\BestSeller;
use Niubi\AbandonedCart\Block\SkuSeller;
use Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart\CollectionFactory;
use Niubi\Core\Helper\Data;
use Niubi\AbandonedCart\Logger\Logger;
use Niubi\Core\Helper\Mail;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $_quotesFactory;
    protected $_timezoneInterface;
    protected $_timezone;
    protected $_logger;
    protected $_data;
    protected $_abandonedCartFactory;
    protected $_abandonedCartCollection;
    protected $_mail;
    protected $_itemFactory;
    protected $_imageHelper;
    protected $_bestSeller;
    protected $_skuSeller;

    public function __construct(
        Context $context,
        Data $data,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone,
        \Niubi\AbandonedCart\Model\AbandonedCartFactory $_abandonedCart,
        CollectionFactory $abandonedCartCollection,
        BestSeller $bestSeller,
        SkuSeller $skuSeller,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $itemFactory,
        Logger $logger,
        Mail $mail
    )
    {
        $this->_skuSeller = $skuSeller;
        $this->_bestSeller = $bestSeller;
        $this->_data = $data;
        $this->_quotesFactory = $quotesFactory;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_timezone = $timezone;
        $this->_logger = $logger;
        $this->_abandonedCartCollection = $abandonedCartCollection;
        $this->_abandonedCartFactory = $_abandonedCart;
        $this->_mail = $mail;
        $this->_itemFactory = $itemFactory;
        $this->_imageHelper = $imageHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        /*$this->_skuSeller->getProductCollection();
        exit();*/


        /**
         * 查询弃购是否开启
         */
        $enabled = $this->_data->getAbandonedConfig('enable');
        if (!$enabled) {
            return false;
        }
        $page = 1;
        $total_page = 100;
        while ($page <= $total_page) {
            /** @var $collection \Magento\Reports\Model\ResourceModel\Quote\Collection */
            $collection = $this->_quotesFactory->create();
            $collection->prepareForAbandonedReport([]);
            $collection->addFieldToFilter('abandoned_send', ['neq' => 1]);
            $collection->setPageSize(100);
            $collection->setCurPage($page);
            $total_page = $collection->getLastPageNumber();
            $page = $page + 1;

            /**
             * 弃购时间
             */
            $time = explode(',', $this->_data->getAbandonedConfig('time'));
            rsort($time);


            /**
             * 修改状态不查询
             */
            foreach ($collection as $item) {
                $quote = $item->toArray();

                /**
                 * @var Quote $item
                 */

                if (!isset($quote['updated_at'])) {
                    continue;
                }

                $form = time();
                $to = strtotime($quote['updated_at']);

                foreach ($time as $k => $value) {
                    if ($form - $to > $value * 3600) {

                        //如果是最大的
                        /**
                         * @var  Collection $abandonedCart
                         */
                        $abandonedCart = $this->_abandonedCartCollection->create();

                        $abandonedCart = $abandonedCart->addFieldToFilter('customer_id', ['eq' => $quote['customer_id']])
                            ->addFieldToFilter('cart_id', ['eq' => $quote['entity_id']])
                            ->addFieldToFilter('hour', ['eq' => $value])
                            ->addFieldToFilter('is_display', ['eq' => 0])
                            ->getFirstItem();

                        //查询
                        if (empty($abandonedCart->toArray())) {

                            $this->_logger->info($quote['customer_id'] . '进来');

                            $abandonedCartFactory = $this->_abandonedCartFactory->create();
                            $abandonedCartFactory->addData(
                                [
                                    'customer_id' => $quote['customer_id'],
                                    'cart_id' => $quote['entity_id'],
                                    'hour' => $value,
                                    'created_time' => date('Y-m-d H:i:s', time()),
                                    'is_display' => 0
                                ]
                            );
                            $abandonedCartFactory->save();
                            //如果是最大的
                            if ($k == 0) {
                                $item->setAbandonedSend(1);
                                $item->save();
                            }
                            /**
                             * 邮件内容
                             */
                            switch ($k) {
                                case 0:
                                    $email_desc = $this->_data->getAbandonedConfig('tel2');
                                    //标题
                                    $email_title = $this->_data->getAbandonedConfig('title2');
                                    $a = 3;
                                    break;
                                case 1:
                                    $email_desc = $this->_data->getAbandonedConfig('tel1');
                                    $a = 1;
                                    $email_title = $this->_data->getAbandonedConfig('title1');
                                    break;
                                case 2:
                                    $email_desc = $this->_data->getAbandonedConfig('tel');
                                    $email_title = $this->_data->getAbandonedConfig('title');
                                    $a = 1;
                                    break;
                                default :
                                    $a = 1;
                                    $email_desc = $this->_data->getAbandonedConfig('tel');
                                    $email_title = $this->_data->getAbandonedConfig('title');
                                    break;
                            }

                            $this->sendEmailByQuote($item, $email_desc, $email_title, $a);
                            /*    $this->_mail->sendMail($email_title, $email_desc, '', $quote['customer_email'], $quote['customer_firstname']);*/
                            //跳出此次循环
                            break;
                        }
                    }
                }
            }


        }

    }

    protected function getQuoteDetail(\Magento\Quote\Model\Quote $quote)
    {

        /**
         * @var \Magento\Quote\Model\Quote $item1
         */
        $quoteItems = $quote->getItemsCollection();
        $data = [];

        /**
         * @var Item $quoteItem
         */
        foreach ($quoteItems as $k => $quoteItem) {

            $product = $quoteItem->getProduct();
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
            /**
             * @var Product $product
             */
            $data[$k]['product']['id'] = $product->getId();
            $data[$k]['product']['price'] = $product->getFinalPrice();
            $data[$k]['product']['name'] = $product->getName();
            $data[$k]['product']['url'] = $product->getProductUrl();
            if (@$product->getImage()) {
                $data[$k]['product']['image'] = $this->_imageHelper->init($product->getImage(), 'small_image')
                    ->setImageFile($product->getImage()) // image,small_image,thumbnail
                    ->resize(380)
                    ->getUrl();
            } else {
                $data[$k]['product']['image'] = '';
            }
            $relatedProducts = $product->getRelatedProducts();
            if (!empty($relatedProducts)) {

                foreach ($relatedProducts as $k1 => $relatedProduct) {
                    /**
                     * @var Product $_product
                     */

                    //    var_dump($relatedProduct instanceof  Product);
                    $_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($relatedProduct->getId());
                    $data[$k]['product']['relatedProduct'][$k1]['id'] = $_product->getId();
                    $data[$k]['product']['relatedProduct'][$k1]['price'] = $_product->getPrice();
                    $data[$k]['product']['relatedProduct'][$k1]['name'] = $_product->getName();
                    $data[$k]['product']['relatedProduct'][$k1]['url'] = $_product->getFinalPrice();

                    $data[$k]['product']['relatedProduct'][$k1]['image'] = $this->_imageHelper->init($_product->getImage(), 'small_image')
                        ->setImageFile($_product->getImage()) // image,small_image,thumbnail
                        ->resize(380)
                        ->getUrl();
                }
            }

        }

        return $data;
        //  var_dump($aa->getSelect()->__toString());
        //  var_dump(1111);
    }

    protected function replaceCartGoods($item)
    {
        $cartStart = <<<heredoc
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnBoxedTextBlock" style="min-width:100%;">
                                    <!--[if gte mso 9]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <![endif]-->
                                    <tbody class="mcnBoxedTextBlockOuter">
                                        <tr>
                                            <td valign="top" class="mcnBoxedTextBlockInner">
                                                
                                                <!--[if gte mso 9]>
                                                <td align="center" valign="top" ">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnBoxedTextContentContainer">
                                                    <tbody>
                                                  
heredoc;
        $cartend = <<<heredoc
                                    </tbody>
                                        
                                        </table>
                                    
                                    </td>
                                </tr>
                            </tbody>
                        </table>
heredoc;
        $price = number_format($item['product']['price'], 2);

        $cartStart .= <<<heredoc
<a href="{$item['product']['url']}?utm_source=email&utm_medium=paid/Fauth" style="text-decoration:none;
color:#ffffff;">
					<tr>
                        
                        <td style="padding-top:9px; padding-left:18px; padding-bottom:9px; padding-right:18px;">
                        
                            <table border="0" cellspacing="0" class="mcnTextContentContainer" width="100%" style="min-width: 100% !important;background-color: #FFFFFF;">
                                <tbody>
                                <tr style="border-bottom: 1px solid #efeeea">
                                    <td valign="top" class="mcnTextContent" style="padding: 18px;color: #222222;font-family: Georgia, Times, &quot;Times New Roman&quot;, serif;font-size: 18px;font-weight: normal;text-align: center;">
                                        <img data-file-id="4448385" src="{$item['product']['image']}" style="border: 0px initial ; width: 100px; height: auto; margin: 0px;" > 
                                         </td>
<td> {$item['product']['name']}</td>
<td style="font-size: 16px; width: 80px; font-style: normal;color: #dd127b;"> <span>$</span> {$price}</td>




                                        
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
</a>
heredoc;
        $cartStart .= $cartend;
        return $cartStart;
    }


    protected function replaceRelatedProducts($item, $k)
    {
        if ($k % 2 == 0) {
            $padding = 'left';
        } else {
            $padding = 'right';
        }
        $title = substr($item['name'], 0, 18) . '...';
        $price = number_format($item['price'], 2);
        $start = <<<heredoc
                  <table align="{$padding}" width="273" border="0" cellpadding="0" cellspacing="0"  style="margin-bottom: 20px" class="mcnImageGroupContentContainer">
                            <tbody><tr>
                                <td class="mcnImageGroupContent" valign="top" style="padding-left: 9px; padding-top: 0; padding-bottom: 0;">
                                
                 <a href="{$item['url']}?utm_source=email&utm_medium=paid/Fauth" title="" class="" target="_blank">
                <img alt="" src="{$item['image']}" width="264px" style="max-width:300px; padding-bottom: 0;" class="mcnImage">
             </a>
                                
                                </td>
                             
                            </tr>
                            <tr>
                                <td class="mcnImageGroupContent" valign="top" style="padding-left: 9px; padding-top: 0; padding-bottom: 0;">
                                        {$title}
                                 
                                </td>
                             
                            </tr>
                                <tr>
                                <td  style="color: #dd127b;font-weight: bold;text-align: center" class="mcnImageGroupContent" valign="top" style="padding-left: 9px; padding-top: 0; padding-bottom: 0;">
                                      <span>$</span>{$price}
                      
                                </td>
                                
                            </tr>
   
                                <tr >
                                <td width="30%" class="mcnImageGroupContent" valign="top" style="padding-left: 9px;  padding-top: 0; padding-bottom: 0;">
                                    <table  style="color: #ffffff;height:30px;padding-right:10px;padding-left: 10px; background:#dd127b;font-weight: bold;text-align: center;margin:0 auto"  border="0" cellpadding="0" cellspacing="0" >
                                     <tr>
                                     <td style="padding-left: 5px;padding-right: 5px" >
                                                      <a href="{$item['url']}?utm_source=email&utm_medium=paid/Fauth" title="" style="text-decoration:none;
color:#ffffff;" target="_blank">
                              Buy Now>>         
                              </a>
</td>
</tr>                                   
</table>              
                                </td>                               
                            </tr>
                        </tbody>
                        </table>
heredoc;

        return $start;
    }

    protected function sendEmailByQuote($item1, $email_desc, $email_title, $a)
    {
        $quote = $item1->toArray();
        //标题
        $data = $this->getQuoteDetail($item1);
        $relatedProductTel = '';
        $cartGoods = '';
        $filter = [];
        $filter1 = [];
        $i = 0;
        $start = <<<heredoc
<table border="0" cellpadding="0" cellspacing="0" width="600" class="mcnImageGroupBlock">
    <tbody class="mcnImageGroupBlockOuter">
    <tr>
<td valign="top" style="padding:9px" class="mcnImageGroupBlockInner">
heredoc;
        $end = <<<heredoc
</td>
</tr>
     </tbody>
</table>
heredoc;
        foreach ($data as $item) {
            if (in_array($item['product']['id'], $filter)) {
                continue;
            }
            if (count($filter) > 2) {
                break;
            }
            $filter[] = $item['product']['id'];
            $cartGoods .= $this->replaceCartGoods($item);
            if (!empty($item['product']['relatedProduct'])) {
                foreach ($item['product']['relatedProduct'] as $k => $relatedProduct) {
                    if (in_array($relatedProduct['id'], $filter1)) {
                        continue;
                    }
                    $filter1[] = $relatedProduct['id'];
                    $i = $i + 1;
                    if ($i > 6) {
                        break;
                    }
                    $start = $start . $this->replaceRelatedProducts($relatedProduct, $i);
                }
                $relatedProductTel = $start . $end;

            }
        }
        $email_desc = str_replace('{{cartProducts}}', $cartGoods, $email_desc);
        $email_desc = str_replace('{{relatedProducts}}', $relatedProductTel, $email_desc);
        if (strpos($email_desc, '{{bestSellerProducts}}') !== false) {
            $i = 0;
            $start = <<<heredoc
<table border="0" cellpadding="0" cellspacing="0" width="600" class="mcnImageGroupBlock">
    <tbody class="mcnImageGroupBlockOuter">
    <tr>
<td valign="top" style="padding:9px" class="mcnImageGroupBlockInner">
heredoc;
            $end = <<<heredoc
</td>
</tr>
     </tbody>
</table>
heredoc;
            $collection = $this->_bestSeller->getProductCollection();
            foreach ($collection as $item) {
                $start = $start . $this->replaceRelatedProducts($item, $i);
            }
            $bestSeller = $start . $end;
            $email_desc = str_replace('{{bestSellerProducts}}', $bestSeller, $email_desc);
        }
        $skuCollections = $this->_skuSeller->getProductCollection($email_desc);
        preg_match_all('/{{sku:([\s\S]*?)}}/', $email_desc, $matches);

        if (count($skuCollections)) {
            foreach ($skuCollections as $key => $skuCollection) {


                $i = 0;
                $start = <<<heredoc
<table border="0" cellpadding="0" cellspacing="0" width="600" class="mcnImageGroupBlock">
    <tbody class="mcnImageGroupBlockOuter">
    <tr>
<td valign="top" style="padding:9px" class="mcnImageGroupBlockInner">
heredoc;
                $end = <<<heredoc
</td>
</tr>
     </tbody>
</table>
heredoc;
                foreach ($skuCollection as $item) {
                    $start = $start . $this->replaceRelatedProducts($item, $i);
                }
                $bestSeller = $start . $end;
                $email_desc = str_replace($matches[0][$key], $bestSeller, $email_desc);
            }
        }

        $this->_mail->sendMail($email_title, $email_desc, '', $quote['customer_email'], $quote['customer_firstname'], ['Abandon'.$a]);

    }


}