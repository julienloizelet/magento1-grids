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
class Okaeli_Grids_Model_Adminhtml_System_Config_Backend_Attribute_Column
    extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * Helper
     * @var Okaeli_Grids_Helper_Data
     */
    protected $_helper;

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $helper = $this->_getHelper();
            $cfgAttributes = array();
            $cfgAfters = array();
            foreach ($value as $key => $config) {
                if (!is_array($config) || !isset($config['attribute'])) continue;
                $cfgAttributes[] = $config['attribute'];
                $cfgAfters[] = $config['after'];
                if ($config['attribute'] == $config['after']) {
                    Mage::throwException(
                        $helper->__(
                            Okaeli_Grids_Helper_Config::LOG_MESSAGE_CONFIG_SAME_ATTRIBUTE_AFTER,
                            $config['attribute']
                        )
                    );
                }

                if (count(array_unique($cfgAttributes)) != count($cfgAttributes)) {
                    Mage::throwException(
                        $helper->__(Okaeli_Grids_Helper_Config::LOG_MESSAGE_CONFIG_ATTRIBUTE_ONLY_ONE_CONFIG)
                    );
                }
            }
        }

        parent::_beforeSave();
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