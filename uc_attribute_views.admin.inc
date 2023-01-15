<?php

/**
 * Confirmation form to build the options table.
 */
function uc_attribute_views_build_confirmation($form, $form_state) {
  $caveats = theme('item_list', array(
    'items' => array(
      t('No records will be created for ordered products that use attributes or options that have been deleted since the order was placed'),
      t('No records will be created for ordered products if the original product has been deleted'),
      t('Price, cost and weight adjustment information for ordered products may be inaccurate if those items have changed since the order was placed (the current item values will be used)')
    )
  ));
  $form = confirm_form(
    array(),
    t('Are you sure you want to build the attribute options table ?'),
    'admin/reports/status',
    t('This action will build a table with attribute and option information based on the ordered products in the database.<p><strong>Important caveats:</strong> !caveats.</p>', array('!caveats' => $caveats)),
    t('Build table'),
    t('Cancel')
  );
  // Change the css class of the submit button to something less scary.
  $form['actions']['submit']['#attributes']['class'][0] = 'button-primary';
  return $form;
}

/**
 * Submit handler for file type classification confirmation form.
 * 
 * @see file_type_classify_confirm()
 */
function uc_attribute_views_build_confirmation_submit($form, &$form_state) {
  // First, delete all entries in the helper table uc_ordered_product_options.
  db_delete('uc_ordered_product_options')->execute();
  $efq = new EntityFieldQuery();
  $efq->entityCondition('entity_type', 'uc_order_product');
  $result = $efq->execute();
  $opids = array();
  if (!empty($result['uc_order_product'])) {
    $opids = array_keys($result['uc_order_product']);
  }
  if (!empty($opids)) {
    $batch = array(
      'title' => t('Building option table'),
      'operations' => array(
        array('uc_attribute_views_build_batch', array($opids)),
      ),
      'progress_message' => '',
      'finished' => 'uc_attribute_views_build_finished',
    );
    batch_set($batch);
  }
  else {
    backdrop_set_message(t('No ordered products found.'));
    state_set('options_table_built', REQUEST_TIME);
  }

  $form_state['redirect'] = 'admin/reports/status';
}

