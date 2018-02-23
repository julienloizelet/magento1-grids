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
class Okaeli_Grids_Test_Integration_Admin_ProductGridTest
    extends Okaeli_Grids_Test_Integration_Admin_Abstract
{

    /**
     * Test if feature is disabled when all is disabled
     */
    public function testDisableAllFeature()
    {
        $this->_testDisableAllFeature(Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
    }

    /**
     * Test if product feature is totally disabled
     */
    public function testDisableProductFeature()
    {
        $this->_testDisableFeature(Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
    }

    /**
     * Test if attribute is in grid if in settings
     */
    public function testGoodAttributeSettings()
    {
        $this->_testGoodAttributeSettings('description', Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
    }

    /**
     * Test if attribute is not in grid if bad settings
     */
    public function testBadAttributeSettings()
    {

        $this->_testBadAttributeSettings(Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
    }

}
