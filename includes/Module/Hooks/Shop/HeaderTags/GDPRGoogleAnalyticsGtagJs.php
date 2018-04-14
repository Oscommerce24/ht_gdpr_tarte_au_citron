<?php
/*
 * InfoContactDisplayRecaptha.phpp
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @copyright Portions Copyright osCommerce
 * @license GNU Public License V2.0
 * @version $Id:
*/

  namespace OSC\OM\Module\Hooks\Shop\HeaderTags;

  use OSC\OM\HTML;
  use OSC\OM\Registry;
  use OSC\OM\HTTP;

  class GDPRGoogleAnalyticsGtagJs {

    private $db;
    private $customer;

    public function __construct() {
      $this->db = Registry::get('Db');
      $this->customer = Registry::get('Customer');
    }

    private function getOption() {
      $data = '';

      return $data;
    }

    public function execute() {
      if (defined('MODULE_HEADER_TAGS_GDPR_TARTE_AU_CITRON_PLUGIN_GOOGLE_ANALYTICS_GTAGJS_ACCOUNT_ID')) {
        $output = '<script type="text/javascript">tarteaucitron.user.gtagUa = \'' . MODULE_HEADER_TAGS_GDPR_TARTE_AU_CITRON_PLUGIN_GOOGLE_ANALYTICS_GTAGJS_ACCOUNT_ID . '\';tarteaucitron.user.gtagMore = function () {' . $this->getOption() . '};(tarteaucitron.job = tarteaucitron.job || []).push(\'gtag\');</script>';
        return $output;
      }
    }
  }