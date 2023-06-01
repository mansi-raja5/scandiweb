<?php

namespace App\Models;
use App\Models\Product;
class ProductManager {
  // Existing methods

  public function addProductWithAttributes(Product $product, array $attributes) {
    // Add the product to the database
    // ...

    // Add the product attributes to the database
    foreach ($attributes as $attribute) {
      // Add the attribute to the product
      $product->addAttribute($attribute);

      // Insert the attribute into the product_attributes table
      // ...
    }
  }
}
?>
