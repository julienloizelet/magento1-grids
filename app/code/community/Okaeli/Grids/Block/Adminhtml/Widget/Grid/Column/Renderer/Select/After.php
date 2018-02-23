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
class Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_After extends
    Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select
{
    /**
     * @var array
     */
    protected $_afters;

    /**
     * Retrieve product attributes
     *
     * @return array|string
     */
    protected function _getAfters()
    {
        if ($this->_afters === null) {
            $afters = array();
            if ($type = $this->_getType()) {
                $gridBlock = $this->_getHelper()->emulateGrid($type);
                if (is_object($gridBlock) && is_array($gridBlock->getColumns())) {
                    $columnsCode = array_keys($gridBlock->getColumns());
                    foreach ($columnsCode as $columnCode) {
                        $afters[$columnCode] = $columnCode;
                    }
                }
            }

            $this->_afters = $afters;
        }

        return $this->_afters;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getAfters() as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }

        return parent::_toHtml();
    }

}
