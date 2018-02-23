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
class Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Align extends
    Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select
{
    /**
     * @var array
     */
    protected $_aligns;

    /**
     * Retrieve product attributes
     *
     * @return array|string
     */
    protected function _getAligns()
    {
        if ($this->_aligns === null) {
            $helper = $this->_getHelper();
            $this->_aligns = array(
                'left' => $helper->__('Left'),
                'center' => $helper->__('Center'),
                'right' => $helper->__('Right'),
            );
        }

        return $this->_aligns;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getAligns() as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }

        return parent::_toHtml();
    }

}
