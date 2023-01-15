<?php
/**
 * @file
 * Definition of UcOrderedProductOptionsFilter.
 */

/**
 * Filter handler to filter ordered products by attribute options.
 */
class UcOrderedProductOptionsFilter extends views_handler_filter_many_to_one {
  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();

    $options['attribute'] = array('default' => 0);
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  function has_extra_options() { return TRUE; }

  /**
   * {@inheritdoc}
   */
  function extra_options_form(&$form, &$form_state) {
    $attributes = uc_attribute_load_multiple();
    $options = array();
    foreach ($attributes as $attribute) {
      $options[$attribute->aid] = $attribute->name;
    }

    $form['attribute'] = array(
      '#type' => 'radios',
      '#title' => t('Attribute'),
      '#options' => $options,
      '#description' => t('Select which attribute to show options for.'),
      '#default_value' => $this->options['attribute'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function get_value_options() {
    if (!isset($this->value_options)) {

      $this->value_form_type = 'select';
      $this->value_title = t('Options');
    
      $query = db_select('uc_attribute_options', 'uao');
      $query->condition('aid', $this->options['attribute']);
      $query->fields('uao', array('oid', 'name'));
      $result = $query->execute();
      $options = array();
      foreach ($result as $row) {
        $options[$row->oid] = $row->name;
      }
      if (count($options) == 0) {
        $options[] = t('No options found.');
      }
      $this->value_options = $options;
    }
  }

}