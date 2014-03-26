<?php

/*
Plugin Name: The New WordPress.com smileys
Plugin URI: http://wordpress.org/plugins/new-smileys/
Description: WordPress.com just released new smileys, and this plugin will make them available to self hosted WordPress installs.
Author: Janneke Van Dorpe
Author URI: http://jannekevandorpe.com
Version: 1.0
Text Domain: new-smileys
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

remove_filter( 'the_content', 'convert_smilies' );
remove_filter( 'the_excerpt', 'convert_smilies' );
remove_filter( 'comment_text', 'convert_smilies', 20 );
add_filter( 'the_content', 'janneke_convert_new_smilies' );
add_filter( 'the_excerpt', 'janneke_convert_new_smilies' );
add_filter( 'comment_text', 'janneke_convert_new_smilies', 20 );

remove_action( 'init', 'smilies_init', 5 );
add_action( 'init', 'janneke_new_smilies_init', 5 );

add_action( 'wp_enqueue_scripts', 'janneke_new_smileys_enqueue_scripts' );

function janneke_new_smileys_enqueue_scripts() {
    wp_enqueue_style( 'new-smileys', plugins_url( 'new-smileys.css', __FILE__ ) );
}

function janneke_convert_new_smilies( $text ) {
    global $wp_smiliessearch;
    $output = '';
    if ( get_option( 'use_smilies' ) && ! empty( $wp_smiliessearch ) ) {
        // HTML loop taken from texturize function, could possible be consolidated
        $textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // capture the tags as well as in between
        $stop = count( $textarr );// loop stuff

        // Ignore proessing of specific tags
        $tags_to_ignore = 'code|pre|style|script|textarea';
        $ignore_block_element = '';

        for ( $i = 0; $i < $stop; $i++ ) {
            $content = $textarr[$i];

            // If we're in an ignore block, wait until we find its closing tag
            if ( '' == $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) )  {
                $ignore_block_element = $matches[1];
            }

            // If it's not a tag and not in ignore block
            if ( '' ==  $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] ) {
                $content = preg_replace_callback( $wp_smiliessearch, 'janneke_translate_new_smiley', $content );
            }

            // did we exit ignore block
            if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content )  {
                $ignore_block_element = '';
            }

            $output .= $content;
        }
    } else {
        // return default text.
        $output = $text;
    }
    return $output;
}

function janneke_translate_new_smiley( $matches ) {
    global $wpsmiliestrans;

    if ( count( $matches ) == 0 )
        return '';

    $smiley = trim( reset( $matches ) );
    $img = $wpsmiliestrans[ $smiley ];

    return sprintf( ' <span class="wp-smiley emoji emoji-%s" title="%s">%s</span> ', esc_attr( $img ), esc_attr( $smiley ), esc_attr( $smiley ) );
}

function janneke_new_smilies_init() {
    global $wpsmiliestrans, $wp_smiliessearch;

    // don't bother setting up smilies if they are disabled
    if ( !get_option( 'use_smilies' ) )
        return;

    if ( !isset( $wpsmiliestrans ) ) {
        $wpsmiliestrans = array(
            ':mrgreen:'   => 'mrgreen',
            ':arrow:'     => 'arrow',
            ':twisted:'   => 'evilgrin',
            ':evil:'      => 'evilgrin',
            '&gt;:D'      => 'evilgrin',
            '>:D'         => 'evilgrin',
            ':idea:'      => 'idea',
            ':oops:'      => 'oops',
            ':roll:'      => 'rolleyes',
            ':lol:'       => 'lol',
            'xD'          => 'lol',
            'XD'          => 'lol',
            ':cool:'      => 'cool',
            '8-)'         => 'cool',
            ':sad:'       => 'sad',
            ':('          => 'sad',
            ':-('         => 'sad',
            ':smile:'     => 'smile',
            ':)'          => 'smile',
            ':-)'         => 'smile',
            ':???:'       => 'confused',
            ':?'          => 'confused',
            ':-?'         => 'confused',
            ':grin:'      => 'bigsmile',
            ':D'          => 'bigsmile',
            ':-D'         => 'bigsmile',
            ':razz:'      => 'tongue',
            ':p'          => 'tongue',
            ':P'          => 'tongue',
            ':-p'         => 'tongue',
            ':-P'         => 'tongue',
            ':eek:'       => 'surprised',
            ':shock:'     => 'surprised',
            ':o'          => 'surprised',
            ':-o'         => 'surprised',
            ':O'          => 'surprised',
            ':-O'         => 'surprised',
            '8O'          => 'surprised',
            '8-O'         => 'surprised',
            ':mad:'       => 'angry',
            ':x'          => 'angry',
            ':-x'         => 'angry',
            ':X'          => 'angry',
            ':-X'         => 'angry',
            '&gt;:('      => 'angry',
            '>:('         => 'angry',
            ':neutral:'   => 'neutral',
            ':|'          => 'neutral',
            ':-|'         => 'neutral',
            ':wink:'      => 'wink',
            ';)'          => 'wink',
            ';-)'         => 'wink',
            ':!:'         => 'exclaim',
            ':?:'         => 'question',
            ':heart'      => 'heart',
            '&lt;3'       => 'heart',
            '<3'          => 'heart',
            ':martini:'   => 'martini',
            '&gt;-I'      => 'martini',
            '>-I'         => 'martini',
         ':whiterussian:' => 'whiterussian',
            '|_|'         => 'whiterussian',
            ':burrito:'   => 'burrito',
            'O_o'         => 'mindblown',
            'o_O'         => 'mindblown-alt',
            '(w)'         => 'wordpress',
            '(W)'         => 'wordpress',
            ':star:'      => 'star',
            ':developer:' => 'developer',
            ':bear:'      => 'bear',
            '^^&#8217;'   => 'blush',
            '^^\''        => 'blush',
            '^^’'         => 'blush',
            '^^‘'         => 'blush',
            ':cry:'       => 'cry',
            ':&#8217;('   => 'cry',
            ':\'('        => 'cry',
            ':‘('         => 'cry',
            ':’('         => 'cry',
            ':-/'         => 'uneasy',
            ':/'          => 'uneasy',
            ':-\\'        => 'uneasy',
            ':\\'         => 'uneasy'
        );
    }

    if (count($wpsmiliestrans) == 0) {
        return;
    }

    /*
     * NOTE: we sort the smilies in reverse key order. This is to make sure
     * we match the longest possible smilie (:???: vs :?) as the regular
     * expression used below is first-match
     */
    krsort($wpsmiliestrans);

    $wp_smiliessearch = '/((?:\s|^)';

    $subchar = '';
    foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
        $firstchar = substr($smiley, 0, 1);
        $rest = substr($smiley, 1);

        // new subpattern?
        if ($firstchar != $subchar) {
            if ($subchar != '') {
                $wp_smiliessearch .= ')(?=\s|$))|((?:\s|^)'; ;
            }
            $subchar = $firstchar;
            $wp_smiliessearch .= preg_quote($firstchar, '/') . '(?:';
        } else {
            $wp_smiliessearch .= '|';
        }
        $wp_smiliessearch .= preg_quote($rest, '/');
    }

    $wp_smiliessearch .= ')(?=\s|$))/m';

}
