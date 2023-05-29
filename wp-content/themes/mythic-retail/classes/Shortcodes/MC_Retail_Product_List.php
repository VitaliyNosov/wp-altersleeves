<?php

namespace Mythic_Retail\Shortcodes;

use MC_User_Functions;
use MC_WP;
use Mythic_Core\Abstracts\MC_Shortcode;

class MC_Retail_Product_List extends MC_Shortcode {

    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'retail_products';
    }

    /**
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
        global $wpdb;
        $result = $wpdb->get_col(
            $wpdb->prepare( "
			SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = '%s' 
			ORDER BY pm.meta_value",
                            '_customer_user'
            )
        );

        $numorders = count( $result );
        $details   = MC_WP::meta( 'business_details', MC_User_Functions::id(), 'user' );

        ob_start(); ?>

        <?php

        if( empty( $details ) ) : ?>
            <p>Before you can request wholesale rates for your business, you must first <a
                        href="/dashboard/partner-application">provide your business details</a>. We encourage you to be quick as we will only take 50 orders initially.</p>
        <?php elseif( !empty( $details ) && $numorders > 50 ) :
            // @todo make dynamic for Chad
            ?>
            <p>We appreciate your interest in becoming a retail partner with us. We have filled our first wave of retail partner slots, so you you have been added to the waitlist.</p>
            <p>We are working with larger printing and fulfillment partners and hope to have news regarding additional opportunity in the next few weeks.</p>
            <p>We will keep you updated via email when Mythic Frames become available again.</p>
            <p>Thank you again for your interest and support!</p>
        <?php endif; ?>

        <?php if( ( !empty( $details )  ) || MC_User_Functions::isAdmin() ) : ?>
            <table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Product Design</th>
      <th scope="col">Product Variation</th>
      <th scope="col">Image</th>
      <th scope="col">Price</th>
      <th scope="col">Quantity</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>

      <tr class="table-dark">
          <td></td>
          <td colspan="7">Mythic Frames</td>
      </tr>

      <tr valign="middle">
              <th scope="row">n/a</th>
                  <td colspan="2"><small><strong>Required: Mythic Frames Wholesale Starter Set</strong>

                          <p>This item is essential for your first Mythic Gaming wholesale order: it will be automatically added to <a href="/cart">cart</a> prior to your first order.</p>

                          <p>It contains 4 of Mythic Frames set for each of our 12 designs. Each set contains:</p>
                          <ul>
                            <li>1 x Launch Exclusive Deckbox</li>
                            <li>1 x Design Team Basic Land Booster Pack</li>
                            <li>2 x Design Team Creature Booster Packs</li>
                            <li>2 x Design Team Non-Creature Booster Pack</li>
                        </ul>
                      </small></td>
              <td></td>
            <td>$960</td>
              <td></td>
              <td>

              </td>
            </tr>

      <?php
      $playmats = [ 158, 207, 226, 233, 239, 298, 245, 251, 258, 264, 270, 276 ];
      foreach( $playmats as $product ) :
          $product = wc_get_product( $product );
          if( empty( $product ) ) continue;
          $variations = $product->get_available_variations();
          $variations = wp_list_pluck( $variations, 'variation_id' );

          if( empty( $variations ) ) continue;
          $sorted_variations = [];
          foreach( $variations as $product_id ) {
              $product                    = wc_get_product( $product_id );
              $name                       = strip_tags( $product->get_attribute( 'pack-type' ) );
              $sorted_variations[ $name ] = $product_id;
          }
          ksort( $sorted_variations );

          foreach( $sorted_variations as $product_id ) {
              $product = wc_get_product( $product_id );
              ?>
              <tr valign="middle">
              <th scope="row"><?= $product_id ?></th>
                  <td colspan="2"><small><strong><a
                                      href="<?= $product->get_permalink() ?>"><?= $name = $product->get_formatted_name(); ?></a></strong><br>
                          <?php

                          if( strpos( $name, 'Booster Collection' ) !== false ) {
                              echo '<ul>
<li>4 x Design Team Creature Booster Packs</li>
<li>4 x Design Team Non-Creature Booster Packs</li>
<li>8 x Design Team Basic Land Booster Packs</li>
</ul>';
                          } else if( strpos( $name, 'Set' ) !== false ) {
                              echo '<p>Each set includes</p><ul>
<li>1 x Launch Exclusive Deckbox</li>
<li>1 x Design Team Basic Land Booster Pack</li>
<li>2 x Design Team Creature Booster Packs</li>
<li>2 x Design Team Non-Creature Booster Pack</li>
</ul>';
                          }

                          ?></small></td>
              <td><?= $product->get_image() ?></td>
            <td><?= $product->get_price_html() ?></td>
              <td><input id="quantity-<?= $product_id ?>" type="number" min="1" value="1" style="width:80px;"></td>
              <td>
                  <button class="button add-to-cart" data-product-id="<?= $product_id ?>" style="min-width:200px;">Add to Cart</button><br>
                  <span id="added-<?= $product_id ?>" class="text-success" style="display: none;">Added to cart</span>
              </td>
            </tr>
              <?php
          }
      endforeach; ?>

      <tr class="table-dark">
          <td></td>
          <td colspan="7">Playmats</td>
      </tr>

      <?php
      $playmats = [ 149, 150, 152, 286, 153, 140, 143, 148, 146, 155, 154, 156 ];
      foreach( $playmats as $product_id ) :
          $product = wc_get_product( $product_id );
          ?>
          <tr valign="middle">
              <th scope="row"><?= $product_id ?></th>
              <td colspan="2"><small><?= $product->get_formatted_name(); ?></small></td>
              <td><?= get_the_post_thumbnail( $product_id, 'full' ); ?></td>
            <td><?= $product->get_price_html() ?></td>
              <td><input type="number" min="1" value="1" style="width:80px;"></td>
              <td>
                     <button class="button add-to-cart" data-product-id="<?= $product_id ?>" style="min-width:200px;">Add to Cart</button><br>
                  <span id="added-<?= $product_id ?>" class="text-success" style="display: none;">Added to cart</span>
              </td>
            </tr>
      <?php endforeach; ?>


  </tbody>
</table>
        <?php endif; ?>
        <?php
        $output = ob_get_clean();

        return $output ?? '';
    }

}