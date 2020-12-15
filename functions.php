<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
}

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */

function wpdocs_setup_theme() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size(790, 350, true);
}
add_action( 'after_setup_theme', 'wpdocs_setup_theme' );

add_action( 'init', function() {

    remove_action('homepage', 'storefront_product_categories', 20);

    remove_action( 'storefront_loop_post', 'storefront_post_content', 30 );

    add_action( 'storefront_loop_post', function() {

        echo '<div class="entry-content" itemprop="articleBody">';

        if( has_post_thumbnail() ) {
            echo '<div class="wp-block-image"><figure class="alignright size-medium">';

            the_post_thumbnail( 'medium', [ 'itemprop' => 'image' ] );

            echo '</figure></div>';
        }

        the_excerpt();

        echo '</div>';

    }, 30 );

    remove_action('storefront_post_content_before', 'storefront_post_thumbnail', 10);

    add_action('storefront_post_content_before', function() {
        if ( has_post_thumbnail() ) {
            the_post_thumbnail();
        }
    }, 10);

} );

function collineauxpins_author_info_box( $content ) {

    global $post;

    // Detect if it is a single post with a post author
    if ( is_singular('post') && isset( $post->post_author ) ) {

        // Get author's display name
        $display_name = get_the_author_meta( 'display_name', $post->post_author );

        // If display name is not available then use nickname as display name
        if ( empty( $display_name ) ) {
            $display_name = get_the_author_meta('nickname', $post->post_author);
        }

        // Get author's biographical information or description
        $user_description = get_the_author_meta( 'user_description', $post->post_author );

        // Get author's website URL
        $user_website = get_the_author_meta('url', $post->post_author);

        // Get link to the author archive page
        $user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));

        if ( ! empty( $display_name ) ) {
            $author_details = '<p class="author_name">À propos de ' . $display_name . '</p>';
        }

        if ( ! empty( $user_description ) ) { // Author avatar and bio
            $author_details .= '<p class="author_details">' . get_avatar(get_the_author_meta('user_email'), 90) . nl2br($user_description) . '</p>';
        }

        $author_details .= '<p class="author_links"><a href="'. $user_posts .'">Voir tous les articles de ' . $display_name . '</a></p>';

        // Pass all this info to post content
        $content = $content . '<footer class="author_bio_section" >' . $author_details . '</footer>';
    }
    return $content;
}

add_action( 'the_content', 'collineauxpins_author_info_box' );

function collineauxpins_product_tag_title($title) {
    if( is_product_tag() ) {
        return "Produits identifiés « ". $title ." »";
    }
    return $title;
}

add_filter( 'woocommerce_page_title', 'collineauxpins_product_tag_title' );

add_filter('storefront_credit_link', '__return_false');
