<?php

add_action('admin_post_sdev_import_csv', 'sdev_handle_import_csv');

function afc_write_data($row) {
    if ($row[0]) { $city = ucwords(strtolower($row[0])); }
    if ($row[1]) { $state = strtoupper($row[1]); }
    //if ($row[2]) { $phone = format_phone_number($row[2]); }

    $valid_states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
    if (!in_array($state, $valid_states)) {
        echo 'Invalid state: ' . $state;
        return;
    }

    if (post_exists($city)) {
        echo "Duplicate City: " . $city;
        return;
    } else {
        $title = $city . ', ' . $state;
        $post_data = [
            'post_type' => 'city',
            'post_title' => $title,
            'post_status' => 'publish',
            'tags_input' => $state,
            'meta_input' => [
                '_city' => $city,
                '_state' => $state,
                //'_phone' => $phone
            ]
        ];
        if (!wp_insert_post($post_data)) {
            echo 'Failed to import ' . $row[0];
        }
    }
}

/**
 * Runs the importer and validation checks
 */
function sdev_handle_import_csv() {
    if (!isset($_POST['sdev_import_csv_nonce']) || !wp_verify_nonce($_POST['sdev_import_csv_nonce'], 'sdev_import_csv')) { wp_die('Invalid request.'); }
    
    if (isset($_POST['submit']) && current_user_can('manage_options') && isset($_FILES['sdev_csv_file'])) {
        $csv_file = $_FILES['sdev_csv_file'];
        
        if (!sdev_is_csv($csv_file)) {
            echo "Invalid CSV file.";
            return;
        }

        if (!$check = sdev_is_valid_data($csv_file)) {
            echo "Invalid data:";
            return;
        }
        
        $path = $csv_file['tmp_name'];
        $handle = fopen($path, 'r');
        $first_row = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first_row) {
                $first_row = false;
                continue;
            }
            afc_write_data($row);
        }
    }
}

/**
 * Checks if uploaded file has valid headers
 *
 * @param string $phone phone number from csv file
 * 
 * @return string formatted number, or blank, if number is invalid
 */
function format_phone_number($phone): string {
    // Remove all non-digit characters from the phone number
    $digits = preg_replace('/\D/', '', $phone);
    
    // Check if the number is 10 digits long
    if (strlen($digits) === 10) {
        // Format the number as xxx-xxx-xxxx
        $formatted_number = substr($digits, 0, 3) . '-' . substr($digits, 3, 3) . '-' . substr($digits, 6);
        return $formatted_number;
    }
    
    return '';
}

/**
 * Checks if uploaded file has valid headers
 *
 * @param array $file data array returned from $_FILES
 * 
 * @return bool true if headers are valid, false if not
 */
function sdev_is_valid_headers($file): bool {
    $headers = fgetcsv($file);
    $default_headers = ['city', 'state'];
    $missing_headers = implode(', ', array_diff($default_headers, $headers));

    if (!empty($missing_headers)) { return false; }
    
    return true;
}

/**
 * Checks if uploaded file is a csv
 *
 * @param array $file data array returned from $_FILES
 * 
 * @return bool true if csv, false if not
 */
function sdev_is_csv($file): bool {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $mimeType = $file['type'];
    if ($extension !== 'csv' || $mimeType != 'text/csv') {
        return false;
    } else { return true; }
}

/**
 * Checks the entirety of an imported csv file for invalid data
 *
 * @param array $file data array returned from $_FILES
 * 
 * @return array or NULL array if there are any fails on data checks, null if no failure.
 */
function sdev_is_valid_data($file): ?array {
    $fail_points = null;
    $path = $file['tmp_name'];
    $handle = fopen($path, 'r');
    while (($row = fgetcsv($handle)) !== false) {
        $args = [
            'city' => $row[0],
            'state' => $row[1],
        ];
        if(!sdev_validate_row($args)) {
            $fail_points[] = "invalid data in row " . $row[0] . '<br />';
            continue;
        }
    }
    
    fclose($handle);
    
    return $fail_points;
}

/**
 * Validates data by checking the data against it's expected type or format
 *
 * @param array $args keyed value data from a row in a csv file
 * 
 * @return bool true if valid, false if not
 */
function sdev_validate_row($args): bool {
    //$regex_pattern = '/^\+?(\d{1,3})?[-. (]?\d{3}[-. )]?\d{3}[-. ]?\d{4}$/';
    if (!is_string($args['city']) || !is_string($args['state'])) {
        return false;
    }
    return true;
}