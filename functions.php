<?php
/**
 * Minimal Engineer Theme functions and definitions
 */

if ( ! function_exists( 'minimal_engineer_setup' ) ) :
    function minimal_engineer_setup() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );
        add_theme_support( 'customize-selective-refresh-widgets' );
        add_theme_support( 'responsive-embeds' );
    }
endif;
add_action( 'after_setup_theme', 'minimal_engineer_setup' );

/**
 * Enqueue scripts and styles.
 */
function minimal_engineer_scripts() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

    if ( file_exists( $manifest_path ) ) {
        $manifest = json_decode( file_get_contents( $manifest_path ), true );

        if ( isset( $manifest['index.html']['file'] ) ) {
            wp_enqueue_script( 'minimal-engineer-js', get_template_directory_uri() . '/dist/' . $manifest['index.html']['file'], array(), null, true );
        }

        if ( isset( $manifest['index.html']['css'] ) ) {
            foreach ( $manifest['index.html']['css'] as $index => $css_file ) {
                wp_enqueue_style( 'minimal-engineer-css-' . $index, get_template_directory_uri() . '/dist/' . $css_file, array(), null );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'minimal_engineer_scripts' );

// Filter to allow REST API to return more data
add_filter( 'rest_prepare_post', 'minimal_engineer_rest_prepare_post', 10, 3 );
function minimal_engineer_rest_prepare_post( $data, $post, $request ) {
    $_data = $data->data;
    $_data['author_name'] = get_the_author_meta( 'display_name', $post->post_author );
    $categories = get_the_category( $post->ID );
    $_data['categories_data'] = array_map( function( $cat ) {
        return array(
            'name' => $cat->name,
            'slug' => $cat->slug,
            'term_id' => $cat->term_id
        );
    }, $categories );
    $featured_media_id = get_post_thumbnail_id( $post->ID );
    $_data['featured_image_url'] = $featured_media_id ? get_the_post_thumbnail_url( $post->ID, 'full' ) : null;
    $logo_id = get_post_meta( $post->ID, '_me_app_logo_id', true );
    $_data['app_logo_url'] = $logo_id ? wp_get_attachment_url( $logo_id ) : null;
    $data->data = $_data;
    return $data;
}

/**
 * Customizer settings
 */
function minimal_engineer_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'me_branding', array( 'title' => 'Branding', 'priority' => 30 ) );
    $wp_customize->add_setting( 'me_logo_text', array( 'default' => 'Minimal Engineer', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_logo_text', array( 'label' => 'Logo Text', 'section' => 'me_branding', 'type' => 'text' ) );

    $wp_customize->add_section( 'me_layout', array( 'title' => 'Layout Settings', 'priority' => 32 ) );
    $wp_customize->add_setting( 'me_default_columns', array( 'default' => '1', 'transport' => 'refresh' ) );
    $wp_customize->add_control( 'me_default_columns', array(
        'label' => 'Default List Columns',
        'section' => 'me_layout',
        'type' => 'select',
        'choices' => array( '1' => '1 Column', '2' => '2 Columns', '4' => '4 Columns' )
    ) );

    $wp_customize->add_section( 'me_colors', array( 'title' => 'Theme Colors', 'priority' => 35 ) );
    $wp_customize->add_setting( 'me_primary_color', array( 'default' => '#18181b', 'transport' => 'refresh' ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'me_primary_color', array( 'label' => 'Primary Color', 'section' => 'me_colors' ) ) );

    $wp_customize->add_section( 'me_sns', array( 'title' => 'SNS Links', 'priority' => 40 ) );
    foreach ( array( 'github', 'x', 'youtube', 'qiita', 'zenn' ) as $sns ) {
        $wp_customize->add_setting( "me_sns_$sns", array( 'default' => '', 'transport' => 'refresh' ) );
        $wp_customize->add_control( "me_sns_$sns", array( 'label' => ucfirst( $sns ) . ' URL', 'section' => 'me_sns', 'type' => 'url' ) );
    }
}
add_action( 'customize_register', 'minimal_engineer_customize_register' );

add_action( 'rest_api_init', function() {
    register_rest_route( 'me/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => function() {
            return array(
                'logo_text' => get_theme_mod( 'me_logo_text', 'Minimal Engineer' ),
                'default_columns' => (int) get_theme_mod( 'me_default_columns', 1 ),
                'primary_color' => get_theme_mod( 'me_primary_color', '#18181b' ),
                'sns' => array(
                    'github' => get_theme_mod( 'me_sns_github' ),
                    'x' => get_theme_mod( 'me_sns_x' ),
                    'youtube' => get_theme_mod( 'me_sns_youtube' ),
                    'qiita' => get_theme_mod( 'me_sns_qiita' ),
                    'zenn' => get_theme_mod( 'me_sns_zenn' ),
                )
            );
        },
        'permission_callback' => '__return_true'
    ) );
} );
