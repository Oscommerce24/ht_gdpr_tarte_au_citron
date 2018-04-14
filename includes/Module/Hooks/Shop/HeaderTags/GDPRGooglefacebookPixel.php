<?php
/*
 * InfoContactDisplayRecaptha.phpp
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @copyright Portions Copyright osCommerce
 * @license GNU Public License V2.0
 * @version $Id:
*/

  namespace OSC\OM\Module\Hooks\Shop\HeaderTags;

  use OSC\OM\Registry;

  use ClicShopping\Sites\Shop\Pages\Checkout\Classes\CheckoutSuccess;

  class GDPRGooglefacebookPixel {

    private $db;
    private $customer;

    public function __construct() {
      $this->db = Registry::get('Db');
      $this->customer = Registry::get('Customer');
    }

    private function getOption() {
      global $PHP_SELF, $order_id; //@todo verify

      if ($PHP_SELF == 'checkout_success.php') {
        $data = '';

        $data .= 'fbq(\'track\', \'ViewContent\'); ';
        $data .= 'fbq(\'track\', \'Search\'); ';
        $data .= 'fbq(\'track\', \'AddToCart\'); ';

        $QorderTotal = $this->db->prepare('select value
                                           from :table_orders_total
                                           where orders_id = :orders_id
                                           and (class = :class || class = :class1)
                                          ');
        $QorderTotal->bindInt(':orders_id', $order_id);
        $QorderTotal->bindValue(':class', 'ot_total');
        $QorderTotal->bindValue(':class', 'TO');
        $QorderTotal->execute();

        $Qorder = $this->db->prepare('select currency
                                      from :table_order
                                      where orders_id = :orders_id
                                     ');
        $Qorder->execute();

        $data .= '<script type="text/javascript">' . "\n";
        $data .= 'fbq(\'track\', \'Purchase\', { ';
        $data .= 'content_type: \'product\', ';
        $data .= 'value: '. number_format($QorderTotal->valueDecimal('value'), 2, '.', '');
        $data .= ', currency:  ';
        $data .= $Qorder->value('currency');
        $data .= ' , order_id: ';
        $data .= (int)$order_id;
        $data .= ' , content_ids : ';

        $product_ids = '';

        $QorderProducts = $this->db->prepare('select op.products_id, 
                                                     pd.products_name, 
                                                     op.final_price, 
                                                     op.products_quantity 
                                              from :table_orders_products op,
                                                   :table_products_description pd,
                                                   :table_languages l
                                              where op.orders_id = :orders_id
                                              and op.products_id = pd.products_id 
                                              and l.code =:code
                                             ');
        $QorderProducts->execute();

        $QorderProducts->bindInt(':orders_id', $order_id);
        $QorderProducts->bindValue(':code', DEFAULT_LANGUAGE);

        while ($QorderProducts->fetch()) {
          $product_ids .= (int)$QorderProducts->valueInt('products_id') . ','; // SKU/code - required
        }

        $data .= '\'[' . rtrim($product_ids, ",") . '\]}); ';
        $data .= '</script>' . "\n";
      }
      
      return $data;
    }

    public function execute() {
      if (defined('MODULE_HEADER_TAGS_GDPR_TARTE_AU_CITRON_PLUGIN_FACEBOOK_PIXEL_YOUR_ID')) {
        $output = '<script type="text/javascript">tarteaucitron.user.facebookpixelId = \'' . MODULE_HEADER_TAGS_GDPR_TARTE_AU_CITRON_PLUGIN_FACEBOOK_PIXEL_YOUR_ID . '\'; tarteaucitron.user.facebookpixelMore = function () {' . $this->getOption() . '};(tarteaucitron.job = tarteaucitron.job || []).push(\'facebookpixel\');</script>';

        return $output;
      }
    }
  }