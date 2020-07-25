<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\ExportorderInfo\Controller\Adminhtml\OrderInfo;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\OrderFactory;
/**
 * Class ExportPost
 */
class ExportPost extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
     
    /**
     * @var fileFactory
     */
    protected $fileFactory;
   

    /**
     * Export action from import/export tax
     *
     * @return ResponseInterface
     */
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory,
            \Magento\Framework\App\Response\Http\FileFactory $fileFactory)
    {  
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;     
        parent::__construct($context);
    }
    public function getcsvheader(){
         /** start csv content and set template */
       return $headers = new \Magento\Framework\DataObject(
           [
                'increment_id' => __('ID'),
                'status' => __('Status'),
                'purchase_point'=>('Purchase Point'),
                
               'customer_email' => __('Custome Email'),
               'payment_method' => __('Payment Method'),
               'transaction_id' => __('Transaction ID'),
               'shipping_information' => __('Shipping Informarion'),

        //下单时间
                'created_at'=>__('Purchase Date'),
                //产品信息
                'product_name'=>__('Product Name'),
                'product_sku'=>__('Product Sku'),
                'product_attr'=>__('Product Attr'),
                'product_qty'=>__('Product Qty'),
                'product_price'=>__('Product Price'),
                'discountAmount'=>__('Product DiscountAmount'),
                'product_subtotal'=>__('Product SubTotal'),
                //税 总金额
                'subtotal'=>__('Subtotal'),
                'discount_amount'=>__('Discount Amount'),
                'shipping_amount'=>__('Shipping and Handling'),
                'tax_amount'=>__('Tax Amount'),
                'grand_total'=>('Grand Total'),
                'total_refunded'=>('Total Refunded'),
               
                //账单地址
                'billing_firstname'=>__('Billing First Name'),
                'billing_lastname'=>__('Billing Last Name'),
                'billing_company'=>__('Billing Company'),
                'billing_street'=>__('Billing Street Address'),
                'billing_city'=>__('Billing City'),
                'billing_state'=>__('Billing State/Province'),
                'billing_postcode'=>__('Billing Zip/Postal Code'),
                'billing_country'=>__('Billing Country'),
                'billing_telephone'=>__('Billing Phone Number'),

                //配送地址               
                'shipping_firstname'=>__('Shipping First Name'),
                'shipping_lastname'=>__('Shipping Last Name'),
                'shipping_company'=>__('Shipping Company'),
                'shipping_street'=>__('Shipping Street Address'),
                'shipping_city'=>__('Shipping City'),
                'shipping_state'=>__('Shipping State/Province'),
                'shipping_postcode'=>__('Shipping Zip/Postal Code'),
                'shipping_country'=>__('Shipping Country'),
                'shipping_telephone'=>__('Shipping Phone Number'),
               //发货商信息
                'shipping_trackid' => __('Shipping TrackId'),
                'title' => __('Shippng TiTle'),
                'carrier_code' => __('Carrier Code'),
                'track_number' => __('Track Number')
            ]
        );       
    }
    public function execute()
    {
        $template = '"{{increment_id}}","{{status}}","{{purchase_point}}","{{customer_email}}","{{payment_method}}","{{transaction_id}}","{{shipping_information}}",'                
                . '"{{created_at}}","{{subtotal}}","{{discount_amount}}","{{shipping_amount}}","{{tax_amount}}","{{grand_total}}","{{total_refunded}}",'
                . '"{{billing_firstname}}","{{billing_lastname}}","{{billing_company}}","{{billing_street}}","{{billing_city}}","{{billing_state}}","{{billing_postcode}}","{{billing_country}}","{{billing_telephone}}",'
                . '"{{shipping_firstname}}","{{shipping_lastname}}","{{shipping_company}}","{{shipping_street}}","{{shipping_city}}","{{shipping_state}}","{{shipping_postcode}}","{{shipping_country}}","{{shipping_telephone}}",'
                . '"{{shipping_trackid}}","{{title}}","{{carrier_code}}","{{track_number}}",'
                . '"{{product_name}}","{{product_sku}}","{{product_attr}}","{{product_qty}}","{{product_price}}","{{discountAmount}}","{{product_subtotal}}"';
         
        $content = $this->getcsvheader()->toString($template);
        $content .= "\n";

        $arr = [];
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        foreach ($collection->getItems() as $key => $order) {           
            $purchasepoint = $order->getStoreName();
            $payment = $order->getPayment()->getAdditionalInformation();
            $paymentmethod = $payment['method_title'];
            $transactionid= $order->getPayment()->getLastTransId();

            $billingAddress = $order->getBillingAddress(); //账单地址
             $billingstreet = $billingAddress->getStreet();
            if(count($billingstreet) > 1){
                $billingstreet = $billingstreet[0].'/'.$billingstreet[1];
            }else{
                $billingstreet = $billingstreet[0];
            }
            
            $shippingAddress = $order->getShippingAddress(); //配送地址
            $shippingstreet = $shippingAddress->getStreet();
            if(count($shippingstreet) > 1){
                $shippingstreet = $shippingstreet[0].'/'.$shippingstreet[1];
            }else{
                $shippingstreet = $shippingstreet[0];
            }

            $proitem = array();
            foreach ($order->getAllItems() as $ki => $_item) { //产品信息
                $parentid = $_item ->getParentItemId();
                if(!$parentid){
                    $options = $_item->getProductOptions();
                    if(gettype($options) == 'array'){
                        $options = $options;
                    }else{
                        $options = unserialize($options);
                    }                   
                    if(isset($options['attributes_info'])){
                        $attr = array();
                        foreach ($options['attributes_info'] as $o => $_option) {
                            $attr[$o] = $_option['label'] . ':' . $_option['value'];
                        } 
                        $proitem[$ki]['Attr1'] = implode('&',$attr);
                    }else{
                        $proitem[$ki]['Attr1'] = '';
                    }
                    if(isset($options['options'])){
                        $attr1 = array(); 
                        foreach ($options['options'] as $o => $_op) {
                            $attr1[$o] = $_op['label'] . ':' . $_op['value'];
                        } 
                        $proitem[$ki]['Attr2'] = implode('&',$attr1);
                    }else{
                        $proitem[$ki]['Attr2'] = '';
                    }
                    if($proitem[$ki]['Attr1'] !== ''){
                        $proitem[$ki]['Attr'] =$proitem[$ki]['Attr1'].';'.$proitem[$ki]['Attr2'];
                    }else{
                        $proitem[$ki]['Attr'] = $proitem[$ki]['Attr2'];
                    }
                    $proitem[$ki]['Name'] = $_item->getName();
                    $proitem[$ki]['Sku'] = $_item->getSku();
                    
                    $proitem[$ki]['Qty'] = number_format($_item->getQtyOrdered(),0);
                    $proitem[$ki]['Price'] = number_format($_item->getPrice(),2);
                    $proitem[$ki]['DiscountAmount'] = number_format($_item->getDiscountAmount(),2);
                    $proitem[$ki]['SubTotal'] = number_format($_item->getRowTotal() - $_item->getDiscountAmount(),2);
                } 
            }
           // var_dump($proitem);echo '<br/>';
           // exit;
            $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection();
            $tblMain = $conn->getTableName('sales_shipment_track');
            $select = $conn->select()
                    ->distinct()
                    ->from([ 'sst' => $tblMain], ['entity_id','title', 'carrier_code', 'track_number'])
                    ->where('order_id = ?', $order->getId());
            $data = $conn->fetchAll($select);
            
            $arr = array();
            foreach ($proitem as $pk => $_product) {
                foreach ($data as $k => $value) {
                    // $arr['entity_id'] =  $order->getId();
                    $arr[$pk][$k]['increment_id'] = $order->getIncrementId();
                    $arr[$pk][$k]['status'] = $order->getStatus();
                    $arr[$pk][$k]['purchase_point'] = $purchasepoint;
                    $arr[$pk][$k]['customer_email'] = $order->getCustomerEmail();
                    $arr[$pk][$k]['payment_method'] = $paymentmethod;
                    $arr[$pk][$k]['transaction_id'] = $transactionid;
                    $arr[$pk][$k]['shipping_information'] = $order->getShippingDescription();

                    //下单时间
                    $arr[$pk][$k]['created_at'] = $order->getCreatedAt();
//                'products_ordered'=>__('Products Item'),
                    //税 总金额
                    $arr[$pk][$k]['subtotal'] = number_format($order->getSubtotal(), 2);
                    $arr[$pk][$k]['discount_amount'] = number_format($order->getDiscountAmount(), 2);
                    $arr[$pk][$k]['shipping_amount'] = number_format($order->getShippingAmount(), 2);
                    $arr[$pk][$k]['tax_amount'] = number_format($order->getTaxAmount(), 2);
                    $arr[$pk][$k]['grand_total'] = number_format($order->getGrandTotal(), 2);
                    $arr[$pk][$k]['total_refunded'] = number_format($order->getTotalRefunded(), 2);

                    //账单地址
                    $arr[$pk][$k]['billing_firstname'] = $billingAddress->getFirstName();
                    $arr[$pk][$k]['billing_lastname'] = $billingAddress->getLastName();
                    $arr[$pk][$k]['billing_company'] = $billingAddress->getCompany();
                    $arr[$pk][$k]['billing_street'] = $billingstreet;
                    $arr[$pk][$k]['billing_city'] = $billingAddress->getCity();
                    $arr[$pk][$k]['billing_state'] = $billingAddress->getRegion();
                    $arr[$pk][$k]['billing_postcode'] = $billingAddress->getPostcode();
                    $arr[$pk][$k]['billing_country'] = $billingAddress->getCountry_id();
                    $arr[$pk][$k]['billing_telephone'] = $billingAddress->getTelephone();
                    //配送地址
                    $arr[$pk][$k]['shipping_firstname'] = $shippingAddress->getFirstName();
                    $arr[$pk][$k]['shipping_lastname'] = $shippingAddress->getLastName();
                    $arr[$pk][$k]['shipping_company'] = $shippingAddress->getCompany();
                    $arr[$pk][$k]['shipping_street'] = $shippingstreet;
                    $arr[$pk][$k]['shipping_city'] = $shippingAddress->getCity();
                    $arr[$pk][$k]['shipping_state'] = $shippingAddress->getRegion();
                    $arr[$pk][$k]['shipping_postcode'] = $shippingAddress->getPostcode();
                    $arr[$pk][$k]['shipping_country'] = $shippingAddress->getCountry_id();
                    $arr[$pk][$k]['shipping_telephone'] = $shippingAddress->getTelephone();

                    $arr[$pk][$k]['shipping_trackid'] = $value['entity_id'];
                    $arr[$pk][$k]['title'] = $value['title'];
                    $arr[$pk][$k]['carrier_code'] = $value['carrier_code'];
                    $arr[$pk][$k]['track_number'] = $value['track_number']."\t";

                    //产品信息
                    $arr[$pk][$k]['product_name'] = $_product['Name'];
                    $arr[$pk][$k]['product_sku'] = $_product['Sku'];
                    $arr[$pk][$k]['product_attr'] = $_product['Attr'];
                    $arr[$pk][$k]['product_qty'] = $_product['Qty'];
                    $arr[$pk][$k]['product_price'] = $_product['Price'];
                    $arr[$pk][$k]['discountAmount'] = $_product['DiscountAmount'];
                    $arr[$pk][$k]['product_subtotal'] = $_product['SubTotal'];
                }
                if ($data == NULL) {  //没有发货商信息
                    $arr[$pk][$key]['increment_id'] = $order->getIncrementId();
                    $arr[$pk][$key]['status'] = $order->getStatus();
                    $arr[$pk][$key]['purchase_point'] = $purchasepoint;
                    $arr[$pk][$key]['customer_email'] = $order->getCustomerEmail();
                    $arr[$pk][$key]['payment_method'] = $paymentmethod;
                    $arr[$pk][$key]['transaction_id'] = $transactionid;
                    $arr[$pk][$key]['shipping_information'] = $order->getShippingDescription();

                    //下单时间
                    $arr[$pk][$key]['created_at'] = $order->getCreatedAt();

//                'products_ordered'=>__('Products Item'),
                    //税 总金额
                    $arr[$pk][$key]['subtotal'] = number_format($order->getSubtotal(), 2);
                    $arr[$pk][$key]['discount_amount'] = number_format($order->getDiscountAmount(), 2);
                    $arr[$pk][$key]['shipping_amount'] = number_format($order->getShippingAmount(), 2);
                    $arr[$pk][$key]['tax_amount'] = number_format($order->getTaxAmount(), 2);
                    $arr[$pk][$key]['grand_total'] = number_format($order->getGrandTotal(), 2);
                    $arr[$pk][$key]['total_refunded'] = number_format($order->getTotalRefunded(), 2);

                    //账单地址
                    $arr[$pk][$key]['billing_firstname'] = $billingAddress->getFirstName();
                    $arr[$pk][$key]['billing_lastname'] = $billingAddress->getLastName();
                    $arr[$pk][$key]['billing_company'] = $billingAddress->getCompany();
                    $arr[$pk][$key]['billing_street'] = $billingstreet;
                    $arr[$pk][$key]['billing_city'] = $billingAddress->getCity();
                    $arr[$pk][$key]['billing_state'] = $billingAddress->getRegion();
                    $arr[$pk][$key]['billing_postcode'] = $billingAddress->getPostcode();
                    $arr[$pk][$key]['billing_country'] = $billingAddress->getCountry_id();
                    $arr[$pk][$key]['billing_telephone'] = $billingAddress->getTelephone();
                    //配送地址
                    $arr[$pk][$key]['shipping_firstname'] = $shippingAddress->getFirstName();
                    $arr[$pk][$key]['shipping_lastname'] = $shippingAddress->getLastName();
                    $arr[$pk][$key]['shipping_company'] = $shippingAddress->getCompany();
                    $arr[$pk][$key]['shipping_street'] = $shippingstreet;
                    $arr[$pk][$key]['shipping_city'] = $shippingAddress->getCity();
                    $arr[$pk][$key]['shipping_state'] = $shippingAddress->getRegion();
                    $arr[$pk][$key]['shipping_postcode'] = $shippingAddress->getPostcode();
                    $arr[$pk][$key]['shipping_country'] = $shippingAddress->getCountry_id();
                    $arr[$pk][$key]['shipping_telephone'] = $shippingAddress->getTelephone();

                    $arr[$pk][$key]['shipping_trackid'] = '';
                    $arr[$pk][$key]['title'] = '';
                    $arr[$pk][$key]['carrier_code'] = '';
                    $arr[$pk][$key]['track_number'] = '';

                    //产品信息

                    $arr[$pk][$key]['product_name'] = $_product['Name'];
                    $arr[$pk][$key]['product_sku'] = $_product['Sku'];
                    $arr[$pk][$key]['product_attr'] = $_product['Attr'];
                    $arr[$pk][$key]['product_qty'] = $_product['Qty'];
                    $arr[$pk][$key]['product_price'] = $_product['Price'];
                    $arr[$pk][$key]['discountAmount'] = $_product['DiscountAmount'];
                    $arr[$pk][$key]['product_subtotal'] = $_product['SubTotal'];
                }
            }
            foreach ($arr as $ak => $vo) {
                foreach ($vo as $key => $v) {
//                    var_dump($v);exit;
                    $string = '"' . $v['increment_id'] . '"' .
                            ',"' . $v['status'] . '"' .
                            ',"' . $v['purchase_point'] . '"' .
                            ',"' . $v['customer_email'] . '"' .
                            ',"' . $v['payment_method'] . '"' .
                            ',"' . $v['transaction_id'] . '"' .
                            ',"' . $v['shipping_information'] . '"' .
                            ',"' . $v['created_at'] . '"' .
                            ',"' . $v['subtotal'] . '"' .
                            ',"' . $v['discount_amount'] . '"' .
                            ',"' . $v['shipping_amount'] . '"' .
                            ',"' . $v['tax_amount'] . '"' .
                            ',"' . $v['grand_total'] . '"' .
                            ',"' . $v['total_refunded'] . '"' .
                            ',"' . $v['billing_firstname'] . '"' .
                            ',"' . $v['billing_lastname'] . '"' .
                            ',"' . $v['billing_company'] . '"' .
                            ',"' . $v['billing_street'] . '"' .
                            ',"' . $v['billing_city'] . '"' .
                            ',"' . $v['billing_state'] . '"' .
                            ',"' . $v['billing_postcode'] . '"' .
                            ',"' . $v['billing_country'] . '"' .
                            ',"' . $v['billing_telephone'] . '"' .
                            ',"' . $v['shipping_firstname'] . '"' .
                            ',"' . $v['shipping_lastname'] . '"' .
                            ',"' . $v['shipping_company'] . '"' .
                            ',"' . $v['shipping_street'] . '"' .
                            ',"' . $v['shipping_city'] . '"' .
                            ',"' . $v['shipping_state'] . '"' .
                            ',"' . $v['shipping_postcode'] . '"' .
                            ',"' . $v['shipping_country'] . '"' .
                            ',"' . $v['shipping_telephone'] . '"' .
                            ',"' . $v['shipping_trackid'] . '"' .
                            ',"' . $v['title'] . '"' .
                            ',"' . $v['carrier_code'] . '"' .
                            ',"' . $v['track_number'] . '"' .
                            ',"' . $v['product_name'] . '"' .
                            ',"' . $v['product_sku'] . '"' .
                            ',"' . $v['product_attr'] . '"' .
                            ',"' . $v['product_qty'] . '"' .
                            ',"' . $v['product_price'] . '"' .
                            ',"' . $v['discountAmount'] . '"' .
                            ',"' . $v['product_subtotal'] . '"';
                    $content .= $string . "\n";
                }
            }
        }
       // var_dump($content);exit;
        $date = date('Y-m-d');
        $filename = 'OrderInfo'.$date.'.csv';
        return $this->fileFactory->create($filename, $content, DirectoryList::VAR_DIR);
    }
    
    public function arr2str($arr) {
        foreach ($arr as $v) {
            $a[]  = '"'.$v.'"';
        }
        $string = implode(',',$a);
        return $string;
    }

}
