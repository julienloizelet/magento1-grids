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
class Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select extends Mage_Core_Block_Html_Select
{
    /**
     * Helper
     * @var Okaeli_Grids_Helper_Data
     */
    protected $_helper;

    /**
     * @var string
     */
    protected $_type;

    public function setInputName($value)
    {
        return $this->setName($value);
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

    /**
     * Get grid type
     * @return string
     */
    protected function _getType()
    {
        return $this->_type;
    }

}
