<?php
/**
 * @file
 * Definition of UcAttributesViewsProductOptionsWeightField.
 */

/**
 * Field handler to display weight adjustment for options attached to a product.
 */
class UcAttributesViewsProductOptionsWeightField extends views_handler_field {

  /**
   * Stores the nid of each row to load weight units.
   */
  public $nid_alias;

  /**
   * Stores the product/nodes.
   */
  public $store_nodes;

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();

    $options['option'] = array('default' => 0);
    $options['custom_label'] = array('default' => 'default');
    $options['format'] = array('default' => 'uc_weight');

    return $options;
  }

  /**
  * Provide option to limit to specific attribute.
  */
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['custom_label'] = array(
      '#title' => t('Create a label'),
      '#type' => 'radios',
      '#options' => array(
        'default' => t('Use attribute/option label'),
        'custom' => t('Custom label'),
        'none' => t('No label'),
      ),
      '#default_value' => $this->options['custom_label'],
      '#weight' => -110,
    );

    $form['label']['#states'] = array(
      'visible' => array(
        ':input[name="options[custom_label]"]' => array('value' => 'custom'),
      )
    );

    $aid = $this->definition['aid'];

    $attribute = uc_attribute_load($aid);
    foreach($attribute->options as $option) {
      $options[$option->oid] = $option->name;
    }

    $form['option'] = array(
      '#type' => 'select',
      '#title' => t('Select option'),
      '#description' => t('Show the weight adjustment for the specific options of attribute !name.' , array('!name' => $attribute->name)),
      '#options' => $options,
      '#default_value'=> $this->options['option'],
      '#required' => TRUE,
    );

    $form['format'] = array(
      '#type' => 'radios',
      '#title' => t('Format'),
      '#options' => array(
        'uc_weight' => t('Ubercart weight'),
        'numeric' => t('Numeric'),
      ),
      '#default_value' => $this->options['format'],
      '#weight' => 1,
    );
  }

  /**
   * Overrides views_handler_field::query().
   * 
   * Adds nid of the products to the select query.
   */
  public function query() {
    $this->ensure_my_table();
    $fields = array(
      'nid' => 'nid',
    );
    $this->add_additional_fields($fields);
  }

  /** 
   * Overrides views_handler_field::pre_render().
   * 
   * Loads the specified attribute and its options.
   */
  public function pre_render(&$values) {
    $nid_alias = $this->aliases['nid'];
    foreach ($values as $value) {
      $nids[$value->$nid_alias] = TRUE;
    }
    $this->store_nodes = node_load_multiple(array_keys($nids));
  }

  public function render($values) {
    // $value will contain the aid of the attribute or null.
    $value = $this->get_value($values);
    if (!empty($value)) {
      $nid = $values->{$this->aliases['nid']};
      $product = $this->store_nodes[$nid];
      $weight_unit = $product->weight_units;
      // Get the weight property for the selected option from stored attribute.
      $weight = $this->store_nodes[$nid]->attributes[$value]->options[$this->options['option']]->weight;
      switch ($this->options['format']) {
        case 'uc_weight': 
          $weight = uc_weight_format($weight, $weight_unit);
          break;
      }

      return $weight;
    }
  }

  public function label() {
    if ($this->options['custom_label'] == 'default' && !empty($this->options['option'])) {
      $attribute = uc_attribute_load($this->definition['aid']);
      $oid = $this->options['option'];
      $option_label = $attribute->options[$oid]->name;
      $attribute_option = $attribute->name . '/' . $option_label;
      return t('@attribute_option weight adjustment', array('@attribute_option' => $attribute_option));
    }
    elseif ($this->options['custom_label'] == 'custom' && isset($this->options['label'])) {
      return $this->options['label'];
    }
    return '';
  }
}
