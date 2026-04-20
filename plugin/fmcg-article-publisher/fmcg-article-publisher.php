<?php
/**
 * Plugin Name:  FMCG Article Publisher
 * Description:  Polls a GitHub repository queue file hourly and publishes new articles automatically. WordPress reaches out to GitHub — no inbound firewall rules needed.
 * Version:      1.0.0
 * Author:       fmcg.ie
 * License:      GPL-2.0-or-later
 *
 * Configuration (add to wp-config.php to override defaults):
 *
 *   define( 'FMCG_PUBLISHER_QUEUE_URL', 'https://raw.githubusercontent.com/YOUR_ORG/YOUR_REPO/main/articles/queue.json' );
 *   define( 'FMCG_PUBLISHER_INTERVAL', 'hourly' ); // 'hourly' | 'twicedaily' | 'daily'
 */

defined( 'ABSPATH' ) || exit;

const FMCG_PUBLISHER_HOOK    = 'fmcg_publisher_poll';
const FMCG_PUBLISHER_OPT_IDS = 'fmcg_publisher_published_ids';
const FMCG_PUBLISHER_OPT_LOG = 'fmcg_publisher_last_poll_log';

function fmcg_publisher_queue_url(): string {
    return defined( 'FMCG_PUBLISHER_QUEUE_URL' )
        ? FMCG_PUBLISHER_QUEUE_URL
        : 'https://raw.githubusercontent.com/douglasjbrennan-cmd/fmcg-theme/main/articles/queue.json';
}

function fmcg_publisher_interval(): string {
    return defined( 'FMCG_PUBLISHER_INTERVAL' ) ? FMCG_PUBLISHER_INTERVAL : 'hourly';
}

// ── Activation / deactivation ─────────────────────────────────────────────────

register_activation_hook( __FILE__, function () {
    if ( ! wp_next_scheduled( FMCG_PUBLISHER_HOOK ) ) {
        wp_schedule_event( time(), fmcg_publisher_interval(), FMCG_PUBLISHER_HOOK );
    }
} );

register_deactivation_hook( __FILE__, function () {
    wp_clear_scheduled_hook( FMCG_PUBLISHER_HOOK );
} );

// ── Cron handler ──────────────────────────────────────────────────────────────

add_action( FMCG_PUBLISHER_HOOK, 'fmcg_publisher_poll' );

function fmcg_publisher_poll(): void {
    $log = [ 'time' => current_time( 'mysql' ), 'published' => [], 'skipped' => 0, 'error' => null ];

    $response = wp_remote_get( fmcg_publisher_queue_url(), [ 'timeout' => 15 ] );

    if ( is_wp_error( $response ) ) {
        $log['error'] = $response->get_error_message();
        update_option( FMCG_PUBLISHER_OPT_LOG, $log );
        error_log( 'FMCG Publisher: fetch failed — ' . $log['error'] );
        return;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( $code !== 200 ) {
        $log['error'] = "Queue URL returned HTTP $code";
        update_option( FMCG_PUBLISHER_OPT_LOG, $log );
        error_log( 'FMCG Publisher: ' . $log['error'] );
        return;
    }

    $articles = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $articles ) ) {
        $log['error'] = 'Queue JSON is invalid or empty';
        update_option( FMCG_PUBLISHER_OPT_LOG, $log );
        error_log( 'FMCG Publisher: ' . $log['error'] );
        return;
    }

    $published_ids = (array) get_option( FMCG_PUBLISHER_OPT_IDS, [] );

    foreach ( $articles as $article ) {
        $id     = sanitize_text_field( $article['id'] ?? '' );
        $status = $article['status'] ?? 'pending';

        if ( ! $id || $status !== 'pending' || in_array( $id, $published_ids, true ) ) {
            $log['skipped']++;
            continue;
        }

        $title   = sanitize_text_field( $article['title'] ?? '' );
        $content = wp_kses_post( $article['content'] ?? '' );
        $excerpt = sanitize_textarea_field( $article['excerpt'] ?? '' );

        if ( ! $title || ! $content ) {
            error_log( "FMCG Publisher: skipping '$id' — missing title or content" );
            $log['skipped']++;
            continue;
        }

        $cat_slug = sanitize_title( $article['category_slug'] ?? 'uncategorized' );
        $cat_name = sanitize_text_field( $article['category_name'] ?? ucfirst( $cat_slug ) );
        $cat_id   = fmcg_publisher_get_or_create_category( $cat_slug, $cat_name );

        $post_id = wp_insert_post( [
            'post_title'    => $title,
            'post_content'  => $content,
            'post_excerpt'  => $excerpt,
            'post_status'   => 'publish',
            'post_category' => [ $cat_id ],
        ], true );

        if ( is_wp_error( $post_id ) ) {
            error_log( "FMCG Publisher: failed to publish '$id' — " . $post_id->get_error_message() );
            continue;
        }

        $published_ids[] = $id;
        update_option( FMCG_PUBLISHER_OPT_IDS, $published_ids );

        $log['published'][] = [ 'id' => $id, 'post_id' => $post_id, 'title' => $title ];
        error_log( "FMCG Publisher: published '$title' (post ID $post_id)" );
    }

    update_option( FMCG_PUBLISHER_OPT_LOG, $log );
}

function fmcg_publisher_get_or_create_category( string $slug, string $name ): int {
    $term = get_term_by( 'slug', $slug, 'category' );
    if ( $term ) {
        return (int) $term->term_id;
    }
    $result = wp_insert_term( $name, 'category', [ 'slug' => $slug ] );
    return is_wp_error( $result ) ? 1 : (int) $result['term_id'];
}

// ── Admin page ────────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_management_page(
        'Article Publisher',
        'Article Publisher',
        'manage_options',
        'fmcg-article-publisher',
        'fmcg_publisher_admin_page'
    );
} );

function fmcg_publisher_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Handle "Poll now" action
    if (
        isset( $_POST['fmcg_poll_now'] ) &&
        check_admin_referer( 'fmcg_poll_now' )
    ) {
        fmcg_publisher_poll();
        echo '<div class="notice notice-success"><p>Poll complete — see status below.</p></div>';
    }

    // Handle "Reset published IDs" action
    if (
        isset( $_POST['fmcg_reset_ids'] ) &&
        check_admin_referer( 'fmcg_reset_ids' )
    ) {
        delete_option( FMCG_PUBLISHER_OPT_IDS );
        echo '<div class="notice notice-success"><p>Published IDs cleared — all queue articles will be re-evaluated on the next poll.</p></div>';
    }

    $log           = get_option( FMCG_PUBLISHER_OPT_LOG, null );
    $published_ids = (array) get_option( FMCG_PUBLISHER_OPT_IDS, [] );
    $next_poll     = wp_next_scheduled( FMCG_PUBLISHER_HOOK );
    ?>
    <div class="wrap">
        <h1>FMCG Article Publisher</h1>

        <h2>Configuration</h2>
        <table class="widefat striped" style="max-width:700px">
            <tr><th>Queue URL</th><td><code><?php echo esc_html( fmcg_publisher_queue_url() ); ?></code></td></tr>
            <tr><th>Poll interval</th><td><?php echo esc_html( fmcg_publisher_interval() ); ?></td></tr>
            <tr><th>Next scheduled poll</th><td><?php echo $next_poll ? esc_html( get_date_from_gmt( date( 'Y-m-d H:i:s', $next_poll ) ) ) : '<em>Not scheduled</em>'; ?></td></tr>
        </table>

        <h2>Last Poll</h2>
        <?php if ( $log ) : ?>
            <table class="widefat striped" style="max-width:700px">
                <tr><th>Time</th><td><?php echo esc_html( $log['time'] ); ?></td></tr>
                <tr><th>Articles published</th><td><?php echo count( $log['published'] ); ?></td></tr>
                <tr><th>Skipped / already done</th><td><?php echo (int) $log['skipped']; ?></td></tr>
                <?php if ( $log['error'] ) : ?>
                    <tr><th>Error</th><td style="color:red"><?php echo esc_html( $log['error'] ); ?></td></tr>
                <?php endif; ?>
            </table>
            <?php if ( ! empty( $log['published'] ) ) : ?>
                <h3>Published this poll</h3>
                <ul>
                    <?php foreach ( $log['published'] as $p ) : ?>
                        <li><?php echo esc_html( $p['title'] ); ?> &mdash; <a href="<?php echo esc_url( get_permalink( $p['post_id'] ) ); ?>" target="_blank">View post</a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php else : ?>
            <p><em>No poll has run yet.</em></p>
        <?php endif; ?>

        <h2>Published Article IDs</h2>
        <?php if ( $published_ids ) : ?>
            <ul><?php foreach ( $published_ids as $pid ) echo '<li><code>' . esc_html( $pid ) . '</code></li>'; ?></ul>
        <?php else : ?>
            <p><em>None yet.</em></p>
        <?php endif; ?>

        <h2>Actions</h2>
        <form method="post" style="display:inline-block;margin-right:10px">
            <?php wp_nonce_field( 'fmcg_poll_now' ); ?>
            <input type="submit" name="fmcg_poll_now" class="button button-primary" value="Poll Now">
        </form>
        <form method="post" style="display:inline-block" onsubmit="return confirm('This will re-publish all queue articles. Continue?')">
            <?php wp_nonce_field( 'fmcg_reset_ids' ); ?>
            <input type="submit" name="fmcg_reset_ids" class="button" value="Reset Published IDs">
        </form>
    </div>
    <?php
}
