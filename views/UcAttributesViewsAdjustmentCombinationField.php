<?php
/**
 * @file
 * Definition of UcAttributesViewsAdjustmentCombinationField.
 */

/**
 * Field handler to display all options for products with adjusted SKU.
 */
class UcAttributesViewsAdjustmentCombinationField extends views_handler_field {
  
  public array $attribute_storage;
  public array $nodes;

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options['show_price'] = array('default' => FALSE, 'bool' => TRUE);
    $options['show_cost'] = array('default' => FALSE, 'bool' => TRUE);
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['show_price'] = array(
      '#title' => t('Show sell price adjustment'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['show_price'],
    );
    $form['show_cost'] = array(
      '#title' => t('Show cost adjustment'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['show_cost'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function pre_render(&$values) {
    foreach ($values as $row) {
      if (!empty($row->{$this->field_alias})) {
        if (empty($this->nodes[$row->nid])) {
          $this->nodes[$row->nid] = node_load($row->nid);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function render($values) {
    $combination = $this->get_value($values);
    if (!empty($combination)) {
      $nid = $values->nid;
      $combination = unserialize($combination);
      $node = $this->nodes[$nid];
      $attributes = array();
      foreach ($combination as $aid => $oid) {
        $attribute = $node->attributes[$aid];
        $option = $attribute->options[$oid];
        $attributes[$aid] = "$attribute->name: $option->name";
        if ($this->options['show_price']) {
          $price = $option->price > 0 ? '+' : ($option->price < 0 ? '-' : '');
          $price .= theme('uc_price', array('price' => $option->price));
          $attributes[$aid] .= t(' (Price: !price)', array('!price' => $price)); 
        }
        if ($this->options['show_cost']) {
          $price = $option->cost > 0 ? '+' : ($option->cost < 0 ? '-' : '');
          $price .= theme('uc_price', array('price' => $option->cost));
          $attributes[$aid] .= t(' (Cost: !price)', array('!price' => $price)); 
        }
      }
      return theme('item_list', array('items' => $attributes));
    }
  }
}
