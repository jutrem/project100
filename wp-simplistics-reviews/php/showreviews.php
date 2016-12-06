<?php
    /*
     * Show reviews from database
     */
?>

    <table border="0" style="margin: 0 auto;">
    <tr>
     <th>Name</th>
     <th>Rating</th>
     <th>Comments</th>
    </tr>

<?php
    function outputresults(){
        global $wpdb;
        $result = $wpdb->get_results ( "SELECT * FROM wp_gplace_reviews" );
        foreach ( $result as $review )   {
          echo '<tr>';
          echo '<td>' . $review->author_name .'</td>';
                  echo '<td><span class="reviewstars rating_' . $review->rating  .'"> </span></td>';
                  echo '<td>' . $review->text    .'</td>';
          echo '</tr>';
            }
         }
 outputresults();
?>
        </table>