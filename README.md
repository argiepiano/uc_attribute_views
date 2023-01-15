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
- **Attributes** (field): this field produces a list of the attributes attached to a product (see example). For example, if your "T-shirt" 
has attributes "Size" and "Color", the filter will show both. 
- **Product attribute options** (field): this field allows you to display price, cost or weight
adjustments for a product that has attributes (see example below). For example, if you have defined
an attribute "Size" with three options (Large, Medium, Small) with sell price adjustments for each, you can use this 
field to display the adjustment for a given attribute/option for the product. 
- **Has attributes** (filter): this boolean filter allows you to filter products that have/do not have attributes attached to it.

<img width="1198" alt="Screen Shot 2023-01-14 at 5 59 50 PM" src="https://user-images.githubusercontent.com/9938978/212525168-4b9bb0f5-eb45-42cf-9253-1e79eedb705b.png">


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

<img width="991" alt="Screen Shot 2023-01-14 at 9 08 13 PM" src="https://user-images.githubusercontent.com/9938978/212525206-a38e7514-3bf0-4110-a8a3-427d3b7ea631.png">

Provided handlers for UC Ordered Products
----
Ordered Products are products that belong to an order. This module creates a new table to store adjustments (cost, price and weight) as well as customized option names for Ordered Products. This is done to remediate some poor design in UC Attributes, where options and adjustments are saved as a serialized string in the `uc_order_product` table. The new table created by this module, `uc_ordered_product_options`, is initially populated by looking up the **current** cost, price and weight of the associated options. This means that, if the option's cost, price or weight adjustments have change since the product was originally purchased, or if the original product was deleted, the resulting entries in the new table may NOT be completely accurate, and the Views created with this module may also not be accurate (in terms of the price/cost/weight adjustments). However, after this module is enabled, products in any new purchases are saved in the new table accurately and correctly. 

These handlers are found within the groups "Ordered products" and "Ordered product options".

- **Ordered product ID** (field): shows the internal ID of the ordered product (see example).
- **Attribute options** (filter): filters ordered products by attribute options.
- **Options** (field): shows a list of the chosen options for an ordered product. This field can also display the price adjustment for those options.
- 
<img width="1368" alt="Screen Shot 2023-01-14 at 9 47 53 PM" src="https://user-images.githubusercontent.com/9938978/212525384-8cbca817-1079-4e93-a03d-2141b8ebfbe3.png">


Credits
----
Created and maintained for Backdrop by [argiepiano](https://github.com/argiepiano)

License
-------
This project is GPL v2 software. 
See the LICENSE.txt file in this directory for complete text.
