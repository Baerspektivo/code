<?php
/*
Plugin Name: Cachewarump
Description: 
Version: 1.0
Author:
*/

function benutzerdefiniertes_log($nachricht, $aenderung = null) {
    $log_datei = plugin_dir_path(__FILE__) . 'plugin_log.txt'; 

    $log_nachricht = date('Y-m-d H:i:s') . ': ' . $nachricht;

    if ($aenderung !== null) {
        $log_nachricht .= ' (Änderung: ' . $aenderung . ')';
    }

    $log_nachricht .= PHP_EOL;

  
    file_put_contents($log_datei, $log_nachricht, FILE_APPEND);
}

function get_cached_changes() {
    $cached_changes = get_transient('plugin_cached_changes');

    if (false === $cached_changes) {
        $cached_changes = array();
        set_transient('plugin_cached_changes', $cached_changes, 1000);
    }

    return $cached_changes;
}

function add_cached_change($nachricht) {
    $cached_changes = get_cached_changes();
    $cached_changes[] = $nachricht;
    set_transient('plugin_cached_changes', $cached_changes, 1000);
}

function aufzeichnen_beitrags_erstellung($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return; 
    }

    $post = get_post($post_id);
    if (($post->post_type == 'post' || $post->post_type == 'page') && $post->post_status == 'publish') {
        if ($post->post_type == 'post') {
            $nachricht = 'Ein neuer Beitrag wurde erstellt: ' . $post->post_title;
        } elseif ($post->post_type == 'page') {
            $nachricht = 'Eine neue Seite wurde erstellt: ' . $post->post_title;
        }
        benutzerdefiniertes_log($nachricht);
    }
}

function aufzeichnen_beitrags_aktualisierung($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return; 
    }

   
    $post = get_post($post_id);
    if (($post->post_type == 'post' || $post->post_type == 'page') && $post->post_status == 'publish') {
        if ($post->post_type == 'post') {
            $nachricht = 'Ein Beitrag wurde aktualisiert: ' . $post->post_title;
        } elseif ($post->post_type == 'page') {
            $nachricht = 'Eine Seite wurde aktualisiert: ' . $post->post_title;
        }
        benutzerdefiniertes_log($nachricht, 'Version ' . $post->post_modified);

        add_cached_change($nachricht);
    }
}

add_action('publish_post', 'aufzeichnen_beitrags_erstellung');
add_action('publish_page', 'aufzeichnen_beitrags_erstellung');
add_action('save_post', 'aufzeichnen_beitrags_aktualisierung');
