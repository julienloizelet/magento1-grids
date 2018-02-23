<?php
/**
 * Okaeli_Grids  Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 3
 * that is bundled with this package in the file LICENSE
 *
 * @category Okaeli
 * @package Okaeli_Grids
 * @copyright  Copyright (c)  2018 Julien Loizelet
 * @author     Julien Loizelet <julienloizelet@okaeli.com>
 * @license    GNU GENERAL PUBLIC LICENSE Version 3
 *
 */

/**
 *
 * @category Okaeli
 * @package  Okaeli_Grids
 * @module   Grids
 * @author   Julien Loizelet <julienloizelet@okaeli.com>
 *
 */
class Okaeli_Grids_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Settings path
     */
    const XML_PATH_ENABLED = 'okaeli_grids/general/enabled';
    const XML_PATH_DEBUG = 'okaeli_grids/general/debug';
    const XML_PATH_PRODUCT_GRID_ENABLED = 'okaeli_grids/product/enabled';
    const XML_PATH_PRODUCT_GRID_ATTRIBUTE = 'okaeli_grids/product/attribute_column';
    const XML_PATH_CUSTOMER_GRID_ENABLED = 'okaeli_grids/customer/enabled';
    const XML_PATH_CUSTOMER_GRID_ATTRIBUTE = 'okaeli_grids/customer/attribute_column';
    const XML_PATH_ORDER_GRID_ENABLED = 'okaeli_grids/order/enabled';
    const XML_PATH_ORDER_GRID_ATTRIBUTE = 'okaeli_grids/order/attribute_column';
    const XML_PATH_CMS_PAGE_GRID_ENABLED = 'okaeli_grids/cms_page/enabled';
    const XML_PATH_CMS_PAGE_GRID_ATTRIBUTE = 'okaeli_grids/cms_page/attribute_column';
    const XML_PATH_CMS_BLOCK_GRID_ENABLED = 'okaeli_grids/cms_block/enabled';
    const XML_PATH_CMS_BLOCK_GRID_ATTRIBUTE = 'okaeli_grids/cms_block/attribute_column';
    /**
     * Log file and messages
     */
    const LOG_FILE = 'okaeli_grids_debug.log';
    const LOG_MESSAGE_DISABLED = 'Okaeli Grids feature is disabled';
    const LOG_MESSAGE_WRONG_SETTINGS = 'Something is wrong with settings';
    const LOG_MESSAGE_WRONG_ATTRIBUTE_CODE = 'There is no attribute with code ';
    const LOG_MESSAGE_CONFIG_SAME_ATTRIBUTE_AFTER = 'Config error : Attribute and After must be different : %s ';
    const LOG_MESSAGE_CONFIG_ATTRIBUTE_ONLY_ONE_CONFIG = 'Config error : Each Attribute must have only one settings';
    /**
     * Type of grids
     */
    const ALL_TYPE = 'all';
    const PRODUCT_TYPE = 'product';
    const CUSTOMER_TYPE = 'customer';
    const ORDER_TYPE = 'order';
    const PAGE_TYPE = 'page';
    const BLOCK_TYPE = 'block';
    /**
     * Config `enabled`
     * @var array
     */
    protected $_isEnabled = array();
    /**
     * Config `debug_log`
     * @var bool
     */
    protected $_isDebugLogEnabled;
    /**
     * @var array
     */
    protected $_attributeSettings = array();

    /**
     * Check in config to know if feature is enabled
     * @param string $type
     * @return bool
     */
    public function isEnabled($type = self::ALL_TYPE)
    {

        if (!isset($this->_isEnabled[$type])) {
            $allEnabled = Mage::getStoreConfig(self::XML_PATH_ENABLED);
            switch ($type) {
                case self::ALL_TYPE:
                    $typeEnabled = $allEnabled;
                    break;
                case self::PRODUCT_TYPE:
                    $typeEnabled = ($allEnabled) ? Mage::getStoreConfig(self::XML_PATH_PRODUCT_GRID_ENABLED) : false;
                    break;
                case self::CUSTOMER_TYPE:
                    $typeEnabled = ($allEnabled) ? Mage::getStoreConfig(self::XML_PATH_CUSTOMER_GRID_ENABLED) : false;
                    break;
                case self::ORDER_TYPE:
                    $typeEnabled = ($allEnabled) ? Mage::getStoreConfig(self::XML_PATH_ORDER_GRID_ENABLED) : false;
                    break;
                case self::PAGE_TYPE:
                    $typeEnabled = ($allEnabled) ? Mage::getStoreConfig(self::XML_PATH_CMS_PAGE_GRID_ENABLED) : false;
                    break;
                case self::BLOCK_TYPE:
                    $typeEnabled = ($allEnabled) ? Mage::getStoreConfig(self::XML_PATH_CMS_BLOCK_GRID_ENABLED) : false;
                    break;
                default:
                    $typeEnabled = false;
            }

            $this->_isEnabled[$type] = $typeEnabled;
        }

        return $this->_isEnabled[$type];
    }

    /**
     * Check in config to know if debug log is enabled
     * @return bool
     */
    public function isDebugLogEnabled()
    {
        if ($this->_isDebugLogEnabled === null) {
            $isDebugLogEnabled = Mage::getStoreConfig(self::XML_PATH_DEBUG);
            $this->_isDebugLogEnabled = ($isDebugLogEnabled) ? $isDebugLogEnabled : false;
        }

        return $this->_isDebugLogEnabled;
    }

    /**
     * Print a message in log file
     * @param string $message
     */
    public function debugLog($message)
    {
        if ($this->isDebugLogEnabled()) {
            Mage::log(print_r($message, true) . "\r\n", Zend_Log::DEBUG, self::LOG_FILE, true);
        }
    }

    /**
     * Get settings used for attributes
     * @param string $type
     */
    public function getAttributesSettings($type)
    {
        if (!isset($this->_attributeSettings[$type])) {
            switch ($type) {
                case self::PRODUCT_TYPE:
                    $attributeSetting = trim(Mage::getStoreConfig(self::XML_PATH_PRODUCT_GRID_ATTRIBUTE));
                    break;
                case self::CUSTOMER_TYPE:
                    $attributeSetting = trim(Mage::getStoreConfig(self::XML_PATH_CUSTOMER_GRID_ATTRIBUTE));
                    break;
                case self::ORDER_TYPE:
                    $attributeSetting = trim(Mage::getStoreConfig(self::XML_PATH_ORDER_GRID_ATTRIBUTE));
                    break;
                case self::PAGE_TYPE:
                    $attributeSetting = trim(Mage::getStoreConfig(self::XML_PATH_CMS_PAGE_GRID_ATTRIBUTE));
                    break;
                case self::BLOCK_TYPE:
                    $attributeSetting = trim(Mage::getStoreConfig(self::XML_PATH_CMS_BLOCK_GRID_ATTRIBUTE));
                    break;
                default:
                    $attributeSetting = '';
            }

            $attributeSetting = !empty($attributeSetting) ? unserialize($attributeSetting) : false;

            $this->_attributeSettings[$type] = $attributeSetting;
        }

        return $this->_attributeSettings[$type];
    }

}
