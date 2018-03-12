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
abstract class Okaeli_Grids_Test_Integration_Admin_Abstract
    extends Codex_Xtest_Xtest_Unit_Admin
{
    /**
     * Helper
     * @var Okaeli_Grids_Helper_Data
     */
    protected $_helper;

    const CONFIG_ALIGN = 's:5:"align";s:4:"left";';
    const CONFIG_WIDTH = 's:5:"width";s:3:"100";';

    /**
     * Get the full path of log
     * @param string $logName
     * @return string
     */
    protected function _getLogFileFullPath($logName)
    {
        return Mage::getBaseDir('log') . DS . $logName;
    }

    /**
     * Clear log file
     * @param string $logName
     */
    protected function _resetLogFile($logName)
    {
        $f = @fopen($this->_getLogFileFullPath($logName), 'r+');
        if ($f !== false) {
            ftruncate($f, 0);
            fclose($f);
        }
    }

    /**
     * Check that a string is in a log
     * @param $string
     * @param $logName
     */
    protected function _assertsLogFileContainsString($string, $logName)
    {
        $logFileContent = file_get_contents($this->_getLogFileFullPath($logName));

        $this->assertContains($string, $logFileContent);
    }

    /**
     * Check that a string is not in a log
     * @param $string
     * @param $logName
     */
    protected function _assertsLogFileDoesntContainString($string, $logName)
    {
        $logFileContent = file_get_contents($this->_getLogFileFullPath($logName));

        $this->assertNotContains($string, $logFileContent);
    }

    /**
     * Update a Magento config
     * @param $path
     * @param $value
     * @param null $store
     */
    protected function _changeMageConfig($path, $value, $store = null)
    {
        Mage::app()->getStore($store)->setConfig($path, $value);
    }

    /**
     * Change config and disable all
     */
    protected function _disableAllFeature()
    {
        $this->_changeMageConfig(Okaeli_Grids_Helper_Config::XML_PATH_ENABLED, '0');
    }

    /**
     * Change config and enable all
     */
    protected function _enableAllFeature()
    {
        $this->_changeMageConfig(Okaeli_Grids_Helper_Config::XML_PATH_ENABLED, '1');
    }

    /**
     * Change config for grid feature
     * @param string $type
     * @param string $value
     */
    protected function _enableFeature($type, $value)
    {
        switch ($type) {
            case Okaeli_Grids_Helper_Config::PRODUCT_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_PRODUCT_GRID_ENABLED;
                break;
            case Okaeli_Grids_Helper_Config::CUSTOMER_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_CUSTOMER_GRID_ENABLED;
                break;
            case Okaeli_Grids_Helper_Config::ORDER_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_ORDER_GRID_ENABLED;
                break;
            case Okaeli_Grids_Helper_Config::PAGE_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_CMS_PAGE_GRID_ENABLED;
                break;
            case Okaeli_Grids_Helper_Config::BLOCK_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_CMS_BLOCK_GRID_ENABLED;
                break;
            case Okaeli_Grids_Helper_Config::SUBSCRIBER_TYPE:
                $configPath = Okaeli_Grids_Helper_Config::XML_PATH_SUBSCRIBER_GRID_ENABLED;
                break;
            default:
                $configPath = false;
        }

        if ($configPath) {
            $this->_changeMageConfig($configPath, $value);
        }
    }

    protected function _testDisableAllFeature($type)
    {
        $this->_disableAllFeature();
        $this->_prepareDebugLog();
        $helper = $this->_getHelper();
        $helper->emulateGrid($type);
        $this->_assertsLogFileContainsString(
            Okaeli_Grids_Helper_Config::LOG_MESSAGE_DISABLED,
            Okaeli_Grids_Helper_Config::LOG_FILE
        );
    }

    /**
     * Test if a feature is well disabled
     * @param string $type
     */
    protected function _testDisableFeature($type)
    {
        $this->_enableAllFeature();
        $this->_enableFeature($type, '0');
        $this->_prepareDebugLog();
        $helper = $this->_getHelper();
        $helper->emulateGrid($type);
        $this->_assertsLogFileContainsString(
            Okaeli_Grids_Helper_Config::LOG_MESSAGE_DISABLED,
            Okaeli_Grids_Helper_Config::LOG_FILE
        );
    }

    /**
     * Test if attribute is in grid if setting is ok
     * @param string $attribute
     * @param string $type
     */
    protected function _testGoodAttributeSettings($attribute, $type)
    {
        $this->_enableAllFeature();
        $this->_enableFeature($type, '1');
        $this->_prepareDebugLog();
        $this->_setGoodAttribute($type);
        $helper = $this->_getHelper();
        $gridBlock = $helper->emulateGrid($type);
        $this->assertContains($attribute, array_keys($gridBlock->getColumns()));
    }

    /**
     * Test if there is error message in log if setting is nok
     * @param string $type
     */
    protected function _testBadAttributeSettings($type)
    {
        $this->_enableAllFeature();
        $this->_enableFeature($type, '1');
        $this->_prepareDebugLog();
        $this->_setBadAttribute($type);
        $helper = $this->_getHelper();
        $helper->emulateGrid($type);
        $this->_assertsLogFileContainsString(
            Okaeli_Grids_Helper_Config::LOG_MESSAGE_WRONG_ATTRIBUTE_CODE . 'here-is-a-non-existent-attribute-code',
            Okaeli_Grids_Helper_Config::LOG_FILE
        );
    }

    /**
     * Change config and set a good attribute
     */
    protected function _setGoodAttribute($type)
    {
        switch ($type) {
            case Okaeli_Grids_Helper_Config::PRODUCT_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_PRODUCT_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519898319898_898";a:4:{s:9:"attribute";s:11:"description";s:5:"after";s:4:"name";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::CUSTOMER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CUSTOMER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";a:4:{s:9:"attribute";s:9:"firstname";' .
                    's:5:"after";s:10:"massaction";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::ORDER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_ORDER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";a:4:{s:9:"attribute";s:11:"customer_id";' .
                    's:5:"after";s:13:"real_order_id";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::PAGE_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CMS_PAGE_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";a:4:{s:9:"attribute";s:7:"content";' .
                    's:5:"after";s:5:"title";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::BLOCK_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CMS_BLOCK_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";a:4:{s:9:"attribute";s:7:"content";' .
                    's:5:"after";s:5:"title";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::SUBSCRIBER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_SUBSCRIBER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";a:4:{s:9:"attribute";s:11:"customer_id";' .
                    's:5:"after";s:8:"lastname";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            default:
                break;
        }
    }

    /**
     * Change config and set a bad attribute
     */
    protected function _setBadAttribute($type)
    {
        switch ($type) {
            case Okaeli_Grids_Helper_Config::PRODUCT_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_PRODUCT_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519898319898_898";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:4:"name";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::CUSTOMER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CUSTOMER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:10:"massaction";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::ORDER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_ORDER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:13:"real_order_id";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::PAGE_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CMS_PAGE_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:5:"title";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::BLOCK_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_CMS_BLOCK_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:5:"title";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            case Okaeli_Grids_Helper_Config::SUBSCRIBER_TYPE:
                $this->_changeMageConfig(
                    Okaeli_Grids_Helper_Config::XML_PATH_SUBSCRIBER_GRID_ATTRIBUTE,
                    'a:1:{s:18:"_1519906973139_139";' .
                    'a:4:{s:9:"attribute";s:37:"here-is-a-non-existent-attribute-code";' .
                    's:5:"after";s:8:"lastname";' .
                    self::CONFIG_ALIGN . self::CONFIG_WIDTH . '}}'
                );
                break;
            default:
                break;
        }
    }

    /**
     * Change config and enable debug log
     */
    protected function _enableDebugLog()
    {
        $this->_changeMageConfig(Okaeli_Grids_Helper_Config::XML_PATH_DEBUG, '1');
    }

    /**
     * Enable debug log and clean log file
     */
    protected function _prepareDebugLog()
    {
        $this->_enableDebugLog();
        $this->_resetLogFile(Okaeli_Grids_Helper_Config::LOG_FILE);
    }

    /**
     * Get the helper
     * @return Okaeli_Grids_Helper_Data
     */
    protected function _getHelper()
    {
        if ($this->_helper === null) {
            $helper = Mage::helper('okaeli_grids');
            $this->_helper = ($helper) ? $helper : false;
        }

        return $this->_helper;
    }
}