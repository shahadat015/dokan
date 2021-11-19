<?php

namespace WeDevs\Dokan\REST;

use WeDevs\Dokan\Abstracts\DokanRESTAdminController;
use WP_REST_Server;

/**
 * Dokan Change log
 *
 * @since DOKAN_LITE_SINCE
 */
class ChangeLogController extends DokanRESTAdminController {
    /**
     * Register all routes related with stores
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace, '/changelog', [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_change_log' ],
                    'permission_callback' => [ $this, 'check_permission' ],
                ],
            ]
        );
    }

    /**
     * Get Change Logs
     *
     * @return WP_REST_Response
     */
    public function get_change_log() {
        require_once DOKAN_INC_DIR . '/Admin/whats-new.php';

        return rest_ensure_response( $changelog );
    }
}
