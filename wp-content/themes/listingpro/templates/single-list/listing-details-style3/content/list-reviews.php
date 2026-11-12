<?php



$NumberRating = listingpro_ratings_numbers(get_the_ID());

$rating = get_post_meta( get_the_ID(), 'listing_rate', true );



if($NumberRating > 0):

    ?>

    <div class="lp-listing-reviews">

        <?php

        listingpro_get_all_reviews_v2($post->ID);

        ?>

    </div>

<?php endif; ?>