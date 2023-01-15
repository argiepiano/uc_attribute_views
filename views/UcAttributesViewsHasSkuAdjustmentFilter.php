<?php
/**
 * @file
 * Definition of UcAttributesViewsHasSkuAdjustmentFilter.
 */

/**
 * Filter handler to filter whether product has/doesn't have SKU adjustment.
 */
class UcAttributesViewsHasSkuAdjustmentFilter extends views_handler_filter_boolean_operator {

  public function query() {
    $this->ensure_my_table();
    $this->query->add_where($this->options['group'], $this->table_alias . '.' . $this->real_field, NULL, $this->value ? 'IS NOT NULL' : 'IS NULL');
  }
}