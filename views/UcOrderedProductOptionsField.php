<?php
/**
 * @file
 * Definition of UcOrderedProductOptionsField.
 */

 /**
 * Field handler to display all options of an ordered product.
 */
class UcOrderedProductOptionsField extends views_handler_field_prerender_list {
  /**
   * {@inheritdoc}
   */
  function init(&$view, &$options) {
    parent::init($view, $options);
    $this->additional_fields['order_product_id'] = array('table' => 'uc_order_products', 'field' => 'order_product_id');
  }

  function option_definition() {
    $options = parent::option_definition();

    $options['limit'] = array('default' => FALSE, 'bool' => TRUE);
    $options['attributes'] = array('default' => array());
    $options['price_format'] = array('default' => 'uc_price');
    $options['component'] = array('default' => '');

    return $options;
  }

  /**
  * Provide option to limit to specific attribute.
  */
 function options_form(&$form, &$form_state) {

  $form['limit'] = array(
    '#type' => 'checkbox',
    '#title' => t('Limit options by attributes'),
    '#description' => t('Limit displayed options to those belonging to specific attributes.'),
    '#default_value'=> $this->options['limit'],
  );

  $attributes = uc_attribute_load_multiple();
  $options = array();
  foreach ($attributes as $attribute) {
    $options[$attribute->aid] = $attribute->name;
  }

  $form['attributes'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Attributes'),
    '#options' => $options,
    '#description' => t('Select which attributes to show options for.'),
    '#default_value' => $this->options['attributes'],
    '#states' => array(
      'visible' => array(
        ':input[name="options[limit]"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['component'] = array(
    '#type' => 'select',
    '#required' => TRUE,
    '#empty_value' => '',
    '#title' => t('Option component to display'),
    '#description' => t('Select which component to display. This field uses the values saved at the time the order was placed, EXCEPT for products ordered before Ubercart Attribute Views was enable.'),
    '#options' => array(
      'option_label' => t('Option label'),
      'cost_adjustment' => t('Option cost adjustment'),
      'price_adjustment' => t('Option sell price adjustment'),
      'weight_adjustment' => t('Option weight adjustment'),
    ),
    '#default_value' => $this->options['component'],
  );

  parent::options_form($form, $form_state);

 }

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
    $this->field_alias = $this->aliases['order_product_id'];
    $order_product_ids = array();
    foreach ($values as $result) {
      if (!empty($result->{$this->aliases['order_product_id']})) {
        $order_product_ids[] = $result->{$this->aliases['order_product_id']};
      }
    }

    if ($order_product_ids) {
      $query = db_select('uc_ordered_product_options', 'po');
      $query->fields('po', array('oid','order_product_id', 'attribute_label', 'option_label', 'cost_adjustment', 'price_adjustment', 'weight_adjustment'));
      $query->join('uc_order_products', 'op', 'op.order_product_id = po.order_product_id');
      $query->addField('op', 'qty');
      $query->join('uc_attribute_options', 'ao', 'ao.oid = po.oid');
      $query->fields('ao', array('name', 'cost', 'price', 'weight'));
      $query->join('uc_attributes', 'a', 'a.aid = ao.aid');
      $query->addField('a', 'name', 'attribute_name');
      $query->join('uc_products', 'p', 'p.nid = po.nid');
      $query->addField('p', 'weight_units');
      $query->condition('po.order_product_id', $order_product_ids);
      $selected_attributes = array_filter($this->options['attributes']);
      if (!empty($this->options['limit']) && !empty($selected_attributes)) {
        $query->condition('ao.aid', $selected_attributes);
      }
      $result = $query->execute();

      foreach ($result as $row) {
        $attribute = '';
        if (empty($selected_attributes) || count($selected_attributes) > 1)  {
          $attribute = check_plain($row->attribute_name) . ': ';
        }
        $qty = $row->qty;
        $output = '';
        switch ($this->options['component']) {
          case 'option_label':
            $output = $attribute . check_plain($row->option_label);
            break;

          case 'cost_adjustment':
            $output = theme('uc_price', array('price' => ($row->cost_adjustment / $qty)));
            break;

          case 'price_adjustment':
            $output = theme('uc_price', array('price' => $row->price_adjustment / $qty));
            break;

          case 'weight_adjustment':
            $output = uc_weight_format($row->weight_adjustment / $qty, $row->weight_units);
            break;

        }
        $this->items[$row->order_product_id][$row->oid]['output'] = $output;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  function render_item($count, $item) {
    return $item['output'];
  }
}