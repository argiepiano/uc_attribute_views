<?php
/**
 * @file
 * Definition of UcAttributesViewsAttributesField.
 */

/**
 * Field handler to display all attributes attached to a product.
 */
class UcAttributesViewsAttributesField extends views_handler_field_prerender_list {
  /**
   * {@inheritdoc}
   */
  function query() {
    $this->add_additional_fields();
  }

  /**
   * {@inheritdoc}
   */
  function pre_render(&$values) {
    $this->field_alias = 'nid';
    foreach ($values as $row) { 
      $node = node_load($row->nid);
      $attributes = array();
      if (!empty($node->attributes)) {
        foreach ($node->attributes as $aid => $attribute) {
          $attributes[$aid]['output'] = $attribute->name;
        }
      }
      $this->items[$row->nid] = $attributes;
    }
  }

  /**
   * {@inheritdoc}
   */
  function render_item($count, $item) {
    return check_plain($item['output']);
  }
}