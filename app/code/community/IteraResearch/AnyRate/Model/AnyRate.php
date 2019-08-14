<?php
/**
 * This file is part of AnyRate extension.
 *
 * @category    IteraResearch
 * @package     IteraResearch_AnyRate
 * @copyright   Copyright (c) 2003-2015 Itera Research, Inc. All rights reserved. (http://www.itera-research.com/)
 * @license     http://www.gnu.org/licenses Lesser General Public License
 */

/**
 * Any rate shipping model
 *
 */
class IteraResearch_AnyRate_Model_AnyRate
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'anyrate';
    protected $_isFixed = true;

    /**
     * Enter description here...
     *
     * @param Mage_Shipping_Model_Rate_Request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
            $customerGroupId = $quote->getCustomerGroupId();
        } else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $customerGroupId = $quote->getCustomerGroupId();
        }
        $this->setFreeBoxes($freeBoxes);
        $result = Mage::getModel('shipping/rate_result');
        $customRates = unserialize($this->getConfigData('rates'));
        foreach ($customRates as $customRate) {
            $rateAvailable = false;
            if ($customRate['customer_group'] == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID || $customRate['customer_group'] == $customerGroupId){
                $rateAvailable = true;
            }
            if ($rateAvailable) {
                if ($this->getConfigData('type') == 'O') { // per order
                    $shippingPrice = $customRate['price'];
                } elseif ($this->getConfigData('type') == 'I') { // per item
                    $shippingPrice = ($request->getPackageQty() * $customRate['price']) - ($this->getFreeBoxes() * $customRate['price']);
                } else {
                    $shippingPrice = false;
                }
                $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
                if ($shippingPrice !== false) {
                    $method = Mage::getModel('shipping/rate_result_method');
                    $method->setCarrier('anyrate');
                    $method->setCarrierTitle($this->getConfigData('title'));
                    $method->setMethod($customRate['code']);
                    $method->setMethodTitle($customRate['title']);
                    if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                        $shippingPrice = '0.00';
                    }
                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $result->append($method);
                }
            }
        }
        return $result;
    }

    public function getAllowedMethods()
    {
        return array (
            'anyrate' => $this->getConfigData('name')
        );
    }
}
