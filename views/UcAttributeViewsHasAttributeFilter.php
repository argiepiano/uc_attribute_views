<?php
/**
 * @file
 * Definition of UcAttributeViewsHasAttributeFilter.
 */

/**
 * Filter handler to filter products that have/not have attributes. 
 */
class UcAttributeViewsHasAttributeFilter extends views_handler_filter_in_operator {
  
  /**
   * {@inheritdoc}
   */
  public function get_value_options() {
    if (!isset($this->value_options)) {
      $attributes = uc_attribute_load_multiple();

      $this->value_title = t('Attributes');

      foreach ($attributes as $aid => $attribute) {
        $attributes_options[$aid] = $attribute->name;
      }
      if (count($attributes_options) == 0) {
        $attributes_options[] = t('No atrributes found.');
      }
      $this->value_options = $attributes_options;
    }
  }
} 