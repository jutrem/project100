<?php
    /*
     * Show reviews from database
     */
?>

<?php
    function outputresults(){
        global $wpdb;
        $result = $wpdb->get_results ( "SELECT * FROM wp_gplace_reviews" );
        echo '<ul id="reviews1" class="reviewlist"></ul> ';
        foreach ( $result as $review )   {
          echo '<tr>';
          echo '<li><label class="reviewuser">' . $review->author_name .'</label>';
                  echo '<span class="reviewstars rating_' . $review->rating  .'"> </span>';
                  echo '<span class="reviewtext">' . $review->text    .'</span>';
          echo '</li>';
            }
         echo '</ul>';
         }

 outputresults();
?>
