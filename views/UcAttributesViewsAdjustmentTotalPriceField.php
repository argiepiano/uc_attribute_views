<?php 

class UcAttributesViewsAdjustmentTotalPriceField extends uc_product_handler_field_price {

  /**
   * Store the combined totals
   */
  public array $total;

  public function query() {
    parent::query();
    $this->aliases['combination'] = $this->field_alias;
    $join = new views_join();
    $extra = array($this->view->base_table . '.vid = uc_products.vid');
    $join->construct('uc_products', $this->view->base_table, 'nid', 'nid', $extra);
    $this->query->add_table('uc_products', $this->relationship, $join);

    $this->additional_fields = array(
      'sell_price' => array(
        'table' => 'uc_products',
        'field' => 'sell_price'
      ),
    );
    $this->add_additional_fields();
  }

  public function pre_render(&$rows) {
    // To minimize the number of select query, we group these by nid and oid
    $combination_alias = $this->aliases['combination'];
    $sell_price_alias = $this->query->get_field_alias('uc_products', 'sell_price');
    foreach ($rows as $row) {
      if (!empty($row->$combination_alias)) {
        $unserial_combination = unserialize($row->$combination_alias);
        $query = db_select('uc_product_options', 'po');
        $query->condition('po.nid', $row->nid)
              ->condition('po.oid', $unserial_combination, 'IN')
              ->addField('po', 'price');
        $results = $query->execute();

        $total = 0.0;
        foreach ($results as $result) {
          $total += $result->price;
        }
        $row->$combination_alias = $total + $row->$sell_price_alias;
      }
    }
  }

}