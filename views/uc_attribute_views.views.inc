<?php

/** 
 * Implements hook_views_data().
 */
function uc_attribute_views_views_data() {
  $data = array();

  // Handlers for product attribute options
  $attributes = uc_attribute_load_multiple();
  foreach ($attributes as $aid => $attribute) {
    $data['uc_product_options_' . $aid]['table']['group'] = t('Product attribute options');
    $data['uc_product_options_' . $aid]['table']['join']['node'] = array(
      'table' => 'uc_product_attributes',
      'left_field' => 'nid',
      'field' => 'nid',
      'extra' => array(
        array(
          'field' => 'aid',
          'value' => $aid,  
        ),
      ),
    );
    $data['uc_product_options_' . $aid]['price'] = array(
      'title' => t('!name options price adjustment', array('!name' => $attribute->name)),
      'help' => t('Product price adjustment for options belonging to attribute !name.', array('!name' => $attribute->name)),
      'real field' => 'aid',
      'field' => array(
        'handler' => 'UcAttributesViewsProductOptionsPriceField',
        //'click sortable' => TRUE,
        'float' => TRUE,
        'aid' => $aid,
        'property' => 'price',
      ),
    );
    $data['uc_product_options_' . $aid]['cost'] = array(
      'title' => t('!name options cost adjustment', array('!name' => $attribute->name)),
      'help' => t('Product cost adjustment for options belonging to attribute !name.', array('!name' => $attribute->name)),
      'real field' => 'aid',
      'field' => array(
        'handler' => 'UcAttributesViewsProductOptionsPriceField',
        'float' => TRUE,
        'aid' => $aid,
        'property' => 'cost',
      ),
    );
    $data['uc_product_options_' . $aid]['weight'] = array(
      'title' => t('!name options weight adjustment', array('!name' => $attribute->name)),
      'help' => t('Product weight adjustment for options belonging to attribute !name.', array('!name' => $attribute->name)),
      'real field' => 'aid',
      'field' => array(
        'handler' => 'UcAttributesViewsProductOptionsWeightField',
        //'click sortable' => TRUE,
        'float' => TRUE,
        'aid' => $aid,
      ),
    );
  }
  $data['uc_product_attributes']['table']['group'] = t('Product attributes');
  $data['uc_product_attributes']['table']['join']['node'] = array(
    'left_field' => 'nid',
    'field' => 'nid',
  );
  $data['uc_product_attributes']['attribute_list'] = array(
    'field' => array(
      'title' => t('Attributes'),
      'help' => t('Attributes attached to the product.'),
      'real field' => 'nid',
      'handler' => 'UcAttributesViewsAttributesField',
    ),
  );
  $data['uc_product_attributes']['aid'] = array(
    'filter' => array(
      'title' => t('Has attribute'),
      'help' => t('Product has attribute.'),
      'handler' => 'UcAttributeViewsHasAttributeFilter'
    )
  );

  // Handlers for ordered product attribute options
  $data['uc_ordered_product_options']['table']['group'] = t('Ordered product options');
  $data['uc_ordered_product_options']['table']['join']['uc_order_products'] = array(
    'left_field' => 'order_product_id',
    'field' => 'order_product_id',
  );

  $data['uc_ordered_product_options']['oid'] = array(
    'title' => t('Option ID'),
    'help' => t('The id of the selected option.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'filter' => array(
      'title' => t('Attribute option'),
      'help' => t('Filter ordered products by attribute options.'),
      'handler' => 'UcOrderedProductOptionsFilter',
    ),
  );
  $data['uc_ordered_product_options']['options_list'] = array(
    'title' => t('Options'),
    'help' => t('Ordered product\'s options provided by product attributes'),
    'field' => array(
      'handler' => 'UcOrderedProductOptionsField',
      'real field' => 'order_product_id'
    ),
  ); 

  // Handlers for product adjustments.
  $data['uc_product_adjustments']['table']['group'] = t('Products with SKU adjustments');
  // Make the fields available when the base table is node.
  $data['uc_product_adjustments']['table']['join']['node'] = array(
    'left_field' => 'nid',
    'field' => 'nid',
  );
  $data['uc_product_adjustments']['combination'] = array(
    'title' => t('Adjustment attributes'),
    'help' => t('The combination of attributes for products whose SKUs have been adjusted.'),
    'field' => array(
      'handler' => 'UcAttributesViewsAdjustmentCombinationField',
    ),
    'filter' => array(
      'title' => t('Has SKU adjustments'),
      'help' => t('Products with SKU adjustments based on attributes.'),
      'handler' => 'UcAttributesViewsHasSkuAdjustmentFilter',
      'label' => t('Has SKU adjustments'),
    ),
  );
  $data['uc_product_adjustments']['model'] = array(
    'title' => t('Adjusted SKU'),
    'help' => t('The adjusted product SKU or model.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'relationship' => array(
      'base' => 'uc_product_stock',
      'base field' => 'sku',
      'relationship field' => 'model',
      'handler' => 'views_handler_relationship',
      'title' => t('Stock from products with SKU adjustments'),
      'help' => t("Stock for products with adjusted SKUs from attributes."),
      'label' => t('Stock from products with SKU adjustments'),
    ),
  );
  foreach ($attributes as $aid => $attribute) {
    $data['uc_product_adjustments']["attr_$aid"] = array(
      'title' => $attribute->name,
      'help' => t('Filter by this attribute if it has an adjusted product SKU.'),
      'filter' => array(
        'handler' => 'UcAttributeViewsAdjustedSkuOptionFilter',
        'aid' => $aid,
        'real field' => 'combination'
      )
    );
  }

  return $data;
}

function uc_attribute_views_views_data_alter(&$data) {
  $data['uc_order_products']['order_product_id'] = array(
    'title' => t('Ordered product ID'),
    'help' => t('The internal ID of the ordered product'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['uc_product_stock']['table']['base'] = array(
    'field' => 'nid',
    'title' => t('Stock'),
    'help' => t('Stock for products.'),
    'defaults' => array(
      'field' => 'sku'
    ),
  );
  $data['uc_product_stock']['nid'] = array(
    'title' => t('Nid'),
    'help' => t('The node ID of the product'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
    ),
  );
  // Add stock relationship to the adjustments table.
  $data['uc_product_stock']['sku']['relationship'] = array(
    'base' => 'uc_product_adjustments',
    'base field' => 'model',
    'relationship field' => 'sku',
    'handler' => 'views_handler_relationship',
    'title' => t('SKU adjustments from stock'),
    'label' => t('SKU adjustments from stock'),
    'help' => t("Products with SKU adjustments from the SKUs in the stock table."),
    'group' => t('Products with SKU adjustments'),
  );
}
