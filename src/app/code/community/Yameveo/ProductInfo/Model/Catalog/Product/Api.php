<?php

/**
 * Extension of Magento Soap API V1
 *
 * @package    Yameveo
 * @author     Andrea De Pirro <andrea.depirro@yameveo.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://www.yameveo.com
 * @see        Mage_Catalog_Model_Product_Api
 */
class Yameveo_ProductInfo_Model_Catalog_Product_Api extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve product info.
     * Optional attributes configurable_attributes_data and configurable_products_data
     * show info on children products and configurable options
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array $attributes
     * @param string $identifierType (sku or null)
     * @return array
     */
    public function info($productId, $store = null, $attributes = array(), $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);
        $attributes = is_null($attributes) ? array() : $attributes;
        $all_attributes = in_array('*', $attributes);
        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku' => $product->getSku(),
            'set' => $product->getAttributeSetId(),
            'type' => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites' => $product->getWebsiteIds()
        );

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes) || $all_attributes) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                    $attribute->getAttributeCode()
                );
            }
        }
        return $this->infoResult($result, $product, $attributes, $store, $all_attributes);
    }

    public function infoResult($result, $product, $attributes, $store, $all_attributes)
    {
        $productId = $product->getId();
        if (in_array('url_complete', $attributes) || $all_attributes) {
            $result['url_complete'] = $product->setStoreId($store)->getProductUrl();
        }
        if (in_array('stock_data', $attributes) || $all_attributes) {
            $result['stock_data'] = Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Item_Api')->items($productId);
        }
        if (in_array('images', $attributes) || $all_attributes) {
            $result['images'] = Mage::getSingleton('Mage_Catalog_Model_Product_Attribute_Media_Api')->items(
                $productId,
                $store
            );
        }
        if (!$product->isSuper() && (in_array('parent_sku', $attributes) || $all_attributes)) {
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            if (!$parentIds) {
                $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            }
            if (isset($parentIds[0])) {
                $parent = Mage::getModel('catalog/product')->load($parentIds[0]);
                $result['parent_sku'] = $parent->getSku();
            }
        } elseif ($product->isConfigurable()) {
            $attributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();
            // configurable_options
            if (in_array('configurable_attributes_data', $attributes) || $all_attributes) {
                $options = array();
                $k = 0;
                foreach ($attributesData as $attribute) {
                    $options[$k]['code'] = $attribute['attribute_code'];
                    foreach ($attribute['values'] as $value) {
                        $value['attribute_code'] = $attribute['attribute_code'];
                        $options[$k]['options'][] = $value;
                    }
                    $k++;
                }
                $result['configurable_attributes_data'] = $options;
                // children
                // @todo use $childProducts = $product->getTypeInstance()->getUsedProducts();
                $childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null, $product);
                $skus = array();
                $i = 0;
                foreach ($childProducts as $childProduct) {
                    $skus[$i]['sku'] = $childProduct->getSku();
                    $j = 0;
                    foreach ($attributesData as $attribute) {
                        $skus[$i]['options'][$j]['label'] = $attribute['label'];
                        $skus[$i]['options'][$j]['attribute_code'] = $attribute['attribute_code'];
                        $skus[$i]['options'][$j]['value_index'] = $childProduct[$attribute['attribute_code']];
                        $j++;
                    }
                    $i++;
                }
                $result['configurable_products_data'] = $skus;
            }
        }

        return $result;
    }

}