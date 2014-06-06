<?php

/**
 * Extension of Magento Soap API V2
 *
 * @package    Yameveo
 * @author     Andrea De Pirro <andrea.depirro@yameveo.com>
 * @link       http://www.yameveo.com
 * @see        Mage_Catalog_Model_Product_Api_V2
 */
class Yameveo_ProductInfo_Model_Catalog_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
{
    /**
     * Retrieve product info.
     * Optional attributes configurable_attributes_data and configurable_products_data
     * show info on children products and confiurable options
     *
     * @param int|string $productId
     * @param string|int $store
     * @param stdClass $attributes
     * @param string $identifierType (sku or null)
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {

        $product = $this->_getProduct($productId, $store, $identifierType);
        $all_attributes = in_array('*', $attributes->attributes);
        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku' => $product->getSku(),
            'set' => $product->getAttributeSetId(),
            'type' => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites' => $product->getWebsiteIds()
        );

        $allAttributes = array();
        if (!empty($attributes->attributes)) {
            $allAttributes = array_merge($allAttributes, $attributes->attributes);
        } else {
            foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
                if ($this->_isAllowedAttribute($attribute, $attributes) || $all_attributes) {
                    $allAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        $_additionalAttributeCodes = array();
        if (!empty($attributes->additional_attributes)) {
            /* @var $_attributeCode string */
            foreach ($attributes->additional_attributes as $_attributeCode) {
                $allAttributes[] = $_attributeCode;
                $_additionalAttributeCodes[] = $_attributeCode;
            }
        }

        $_additionalAttribute = 0;
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes) || $all_attributes) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes) || $all_attributes) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $product
                        ->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
                }
            }
        }
        $standard_attributes = $attributes->attributes;
        return Mage::getSingleton('Channeladvisor_ChannelAdvisor_Model_Catalog_Product_Api')->infoResult(
            $result,
            $product,
            $standard_attributes,
            $store,
            $all_attributes
        );
    }

}
