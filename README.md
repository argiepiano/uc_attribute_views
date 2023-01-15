Ubercart Attribute Views
====
Provides additional Views handlers for Ubercart's Attributes, Options and Adjustments. 
These additional Views fields, filters and relationships are helpful to produce 
more complete Views of Products and Ordered Products. 

Dependencies
------
- Ubercart Attributes (uc_attributes, part of the Ubercart project).

Installation
------------
Install this module using the official Backdrop CMS instructions at
https://backdropcms.org/guide/modules.

Description and examples
======
The handlers provided by this module will allow users to produce more complete
Views of Ubercart Products, and Ubercart Ordered Products

Definitions
----
- Ubercart Products are basically nodes that can be added to the cart. They are created
through Content > Add new content
- Ubercart Ordered Products are the way Ubercart stores products that your customers
have purchased, that is, products that have been put into an order during the checkout process.

Provided handlers for UC Products
-----
These handlers are found under the groups "Product attributes" and "Product attribute options".
- **Attributes** (field): this field produces a list of teh attributes attached to a product (see example). For example, if your "T-shirt" 
has attributes "Size" and "Color", the filter will show both. 
- **Product attribute options** (field): this field allows you to display price, cost or weight
adjustments for a product that has attributes (see example below). For example, if you have defined
an attribute "Size" with three options (Large, Medium, Small) with sell price adjustments for each, you can use this 
field to display the adjustment for a given attribute/option for the product. 
- **Has attributes** (filter): this boolean filter allows you to filter products that have/do not have attributes attached to it.

Provided handlers for UC Products that have SKU adjustments
----
When you add attributes and options to a product, UC Attributes allows you to "adjust" the SKU of each
of those combinations. For example, you can create different SKUs for red-small t-shirts, red-medium, etc. The handlers 
provided here allow you to display and filter some of these adjustments.

These handlers are found under the group "Products with SKU adjustments".

- **Has SKU adjustment** (filter): this filter allows you to filter products that have/do not have SKU adjustments.
- **Adjusted SKU** (field): displays the adjusted SKU for products (see example).
- **Adjustment attributes** (field): displays the attributes for products that have adjusted SKUs (see example).
- **Stock from products with SKU adjustments** (relationship): allows you to create a relationship to the Stock table, so taht you can show, for example, stock levels for products with adjusted SKUs. Before this handler, the only way to check stock level for adjusted SKUs was through the module UC Reports (non-view).
- **[Attribute name]** (filter): filter products with adjusted SKUs by the attribute options.

Provided handlers for UC Ordered Products
----
Ordered Products are products that belong to an order. This modules creates a new table to store adjustments (cost, price and weight) as well as option names for Ordered Products. This is done to remediate some poor design, where options and adjustments were saved partially as a serialized string. The new table `uc_ordered_product_options`, is initially populated by looking up the **current** cost, price and weight of the associated options. This means that, if the option cost, price and weight adjustments have change after the product was purchased, or if the original product has been deleted, the resulting entries in that table may NOT be completely accurate. However, after this module is enabled, any new purchases are saved in the new table accurately and correctly. 

These handlers are found in the group "Ordered products" and "Ordered product options".

- **Ordered product ID** (field): shows the internal ID of the ordered product (see example).
- **Attribute options** (filter): filters ordered products by attribute options.
- **Options** (field): shows a list of the chosen options for an ordered product. This field can also display the price adjustment for those options.

Credits
----
Created and maintained for Backdrop by [argiepiano](https://github.com/argiepiano)

License
-------
This project is GPL v2 software. 
See the LICENSE.txt file in this directory for complete text.
