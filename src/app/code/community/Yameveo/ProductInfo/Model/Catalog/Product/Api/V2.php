<?php

/**
 * Extension of Magento Soap API V2
 *
 * @package    Yameveo
 * @author     Andrea De Pirro <andrea.depirro@yameveo.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://www.yameveo.com
 * @see        Mage_Catalog_Model_Product_Api_V2
 */
class Yameveo_ProductInfo_Model_Catalog_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
{
    /**
     * Retrieve product info.
     * Optional attributes configurable_attributes_data and configurable_products_data
     * show info on children products and configurable options
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
        $result = parent::info($productId, $store, $attributes, $identifierType);
        $standard_attributes = $attributes->attributes;
        return Mage::getSingleton('Yameveo_ProductInfo_Model_Catalog_Product_Api')->infoResult(
            $result,
            $product,
            $standard_attributes,
            $store,
            $all_attributes
        );
    }

}
