<?php
/**
 * This file is part of AnyRate extension.
 *
 * @category    IteraResearch
 * @package     IteraResearch_AnyRate
 * @copyright   Copyright (c) 2003-2015 Itera Research, Inc. All rights reserved. (http://www.itera-research.com/)
 * @license     http://www.gnu.org/licenses Lesser General Public License
 */

class IteraResearch_AnyRate_Model_System_Config_Backend_Rates extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        foreach ($value as $code => $rate) {
            if ($code != '__empty' && empty($value[$code]['code']) ) {
                $value[$code]['code'] = $code;
            }
        }
        $this->setValue($value);
        parent::_beforeSave();
    }
}
