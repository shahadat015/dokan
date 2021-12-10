<?php

namespace WeDevs\Dokan\Admin\Notices;

/**
 * What's new notice handler class
 *
 * @since 3.3.3
 */
class WhatsNew {
    /**
     * Class Constructor
     *
     * @since 3.3.3
     */
    public function __construct() {
        add_action( 'dokan_admin_notices', [ $this, 'show_whats_new_notice' ] );
        add_action( 'wp_ajax_dokan-whats-new-notice', [ $this, 'dismiss_new_notice' ] );
    }

    /**
     * Show update notice
     *
     * @since 1.0
     *
     * @param array $notices
     *
     * @return array
     */
    public function show_whats_new_notice( $notices ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $notices;
        }

        // check if it has already been dismissed
        $versions = get_option( 'dokan_lite_whats_new_versions', array() );

        if ( in_array( DOKAN_PLUGIN_VERSION, $versions ) ) {
            return $notices;
        }

        $notices[] = [
            'type'              => 'info',
            'description'       => __( 'Check What\'s new in Dokan', 'dokan-lite' ),
            'priority'          => 10,
            'show_close_button' => true,
            'ajax_data'         => [
                'dokan_promotion_dismissed' => true,
                'action'                    => 'dokan-whats-new-notice',
                'nonce'                     => wp_create_nonce( 'dokan_admin' ),
            ],
            'actions'           => [
                [
                    'type'   => 'primary',
                    'text'   => __( 'What\'s New', 'dokan-lite' ),
                    'action' => esc_url( add_query_arg( array( 'page' => 'dokan#/changelog?plugin=dokan' ), admin_url( 'admin.php' ) ) ),
                ],
            ],
        ];

        return $notices;
    }

    /**
     * Dismiss new notice
     *
     * @since 1.0
     *
     * @return void
     */
    public function dismiss_new_notice() {
        $post_data = wc_clean( wp_unslash( $_POST ) );

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $post_data['nonce'], 'dokan_admin' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan-lite' ) );
        }

        if ( ! empty( $post_data['dokan_promotion_dismissed'] ) ) {
            $versions = get_option( 'dokan_lite_whats_new_versions', array() );

            if ( ! in_array( DOKAN_PLUGIN_VERSION, $versions ) ) {
                $versions[] = DOKAN_PLUGIN_VERSION;
            }

            update_option( 'dokan_lite_whats_new_versions', $versions );

            wp_send_json_success();
        }
    }
}
