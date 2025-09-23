<?php

/**
 * Code to generate the Settings page
 *
 * @link       https://urtext.ca
 * @since      1.0.0
 *
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/admin/partials
 */

class Urtext_Landing_Tracking_Admin_Display_Settings {

    public function __construct() {
    }

    /**
     * Render the settings page.
     *
     * @since    1.0.0
     */
    public function show_settings_page() {
        if ( isset( $_GET['updated'] ) ) {
            add_settings_error(
                'urtext_options_messages',
                'urtext_options_message',
                esc_html__( 'Settings Saved', 'urtext-landing-tracking' ),
                'updated'
            );
        } else if ( isset( $_GET['deleted'] ) ) {
            add_settings_error(
                'urtext_options_messages',
                'urtext_options_message',
                esc_html__( 'All tracking data deleted', 'urtext-landing-tracking' ),
                'updated'
            );
        }
        settings_errors( 'urtext_options_messages' );
        ?>
        <div class="wrap">
            <style>
                .tracking_code_popup_wrapper {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 999;
                    background-color: #00000020;
                    justify-content: center;
                    align-items:center
                }
                .tracking_code_popup {
                    max-width: 100%;
                    max-height: 100%;
                    width: fit-content;
                    height: fit-content;
                    overflow-y:auto;
                    border: 1px solid #ccc;
                    background: #fff;
                    padding: 20px;
                    z-index: 1000;
                }
                .tracking_code_table td {
                    padding: 3px 5px;
                }
                .tracking_codes_table {
                    border-collapse: collapse;
                    max-width: 100%;
                }
                .tracking_codes_table th {
                    border-bottom: 2px solid #ddd;
                }
                .tracking_codes_table th, .tracking_codes_table td {
                    vertical-align: top;
                    padding: 8px;
                    text-align: left;
                    max-width: 400px;
                    overflow-wrap: break-word;
                }
                #rss_feed_container {
                    width: 100%;
                    max-width: 600px;
                    background: #f1f1f1;
                    border: 1px solid #ccc;
                    padding: 10px;
                    cursor: pointer;
                    user-select: all;
                    overflow-wrap: break-word;
                }
                #rss_feed_container:hover {
                    background: #e1e1e1;
                }
            </style>
            <script>
                let urtext_tracking_codes = <?php echo json_encode( get_option('urtext_landing_tracking_codes', array()) ); ?>;
                
                let categories = <?php echo json_encode(get_categories()); ?>;

                let tags = <?php echo json_encode(get_tags()); ?>;

                let authors = <?php echo json_encode(get_users(array('fields' => array("user_nicename", "display_name")))); ?>;

                function showTrackingCodes() {
                    const container = document.getElementById('tracking_codes_container');
                    container.innerHTML = '';
                    if (!urtext_tracking_codes || urtext_tracking_codes.length === 0) {
                        container.innerHTML = '<p><i>No tracking codes saved yet.</i></p>';
                        return;
                    }
                    const table = document.createElement('table');
                    table.className = 'tracking_codes_table';
                    const header = document.createElement('tr');
                    const headers = ['Date Added', 'Title', 'Code', ''];
                    headers.forEach(text => {
                        const th = document.createElement('th');
                        th.innerText = text;
                        header.appendChild(th);
                    });
                    table.appendChild(header);

                    const trackingCodesSelect = document.getElementById('rss_tracking_codes');
                    trackingCodesSelect.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = "";
                    option.innerText = "Select Tracking Code...";
                    trackingCodesSelect.appendChild(option);

                    urtext_tracking_codes.forEach((code, index) => {
                        const row = document.createElement('tr');
                        const cell1 = document.createElement('td');
                        cell1.innerText = new Date(code.date_added).toLocaleDateString();
                        const cell2 = document.createElement('td');
                        cell2.innerText = code.title || '<i>No Title</i>';
                        const cell3 = document.createElement('td');
                        cell3.innerText = "";
                        for(let [key, value] of Object.entries(code)) {
                            if (key !== 'date_added' && key !== 'title' && key !== 'custom_field_content') {
                                if (cell3.innerText !== "") {
                                    cell3.innerText += "&";
                                }
                                if (key === 'custom_field' && value != "" && code.custom_field_content != "") {
                                    cell3.innerText += value + "=" + encodeURIComponent(code.custom_field_content);
                                } else {
                                    cell3.innerText += key + "=" + encodeURIComponent(value);
                                }
                            }
                        };
                        const option = document.createElement('option');
                        option.setAttribute('title', code.title);
                        option.value = cell3.innerText;
                        if (cell3.innerText.length > 50) {
                            option.innerText = code.title + " - " + cell3.innerText.substring(0, 50) + "...";
                        } else {    
                            option.innerText = code.title + " - " + cell3.innerText;
                        }
                        trackingCodesSelect.appendChild(option);

                        const cell4 = document.createElement('td');
                        const copyButton = document.createElement('button');
                        copyButton.type = 'button';
                        copyButton.innerText = 'Copy';
                        copyButton.title = 'Copy this tracking code to clipboard';
                        copyButton.setAttribute('code', cell3.innerText);
                        copyButton.addEventListener('click', (event) => {
                            event.stopPropagation();
                                navigator.clipboard.writeText(event.target.getAttribute('code'));
                                alert("Copied to clipboard");
                            }
                        );
                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.innerText = 'Delete';
                        deleteButton.title = 'Delete this tracking code';
                        deleteButton.style.marginLeft = '10px';
                        deleteButton.addEventListener('click', (event) => {
                            event.stopPropagation();
                            if (confirm("Are you sure you want to delete this tracking code?")) {
                                document.getElementById('urtext_landing_tracking_submit_button').disabled = false;
                                urtext_tracking_codes.splice(index, 1);
                                showTrackingCodes();
                            }
                        });
                        cell4.appendChild(copyButton);
                        cell4.appendChild(deleteButton);
                        row.appendChild(cell1);
                        row.appendChild(cell2);
                        row.appendChild(cell3);
                        row.appendChild(cell4);
                        table.appendChild(row)
                    });
                    container.appendChild(table);

                    urtext_tracking_codes.forEach((code, index) => {
                    });

                    document.getElementById('urtext_landing_tracking_codes').value = JSON.stringify(urtext_tracking_codes);
                }
                function addTrackingCode(event) {
                    event.stopPropagation();
                    var new_code = {};
                    new_code.date_added = new Date().toISOString();
                    if (document.getElementById('title').value.trim() == "") {
                        alert("Please provide a title for the tracking code.");
                        return;
                    } else {
                        for (code of urtext_tracking_codes) {
                            if (code.title == document.getElementById('title').value.trim()) {
                                alert("A tracking code with this title already exists. Please choose a different title.");
                                return;
                            }
                        }
                        new_code.title = document.getElementById('title').value.trim();
                    }
                    if (document.getElementById('utm_source').value.trim() != "") {
                        new_code.utm_source = document.getElementById('utm_source').value.trim().toLowerCase();
                    }
                    if (document.getElementById('utm_medium').value.trim() != "") {
                        new_code.utm_medium = document.getElementById('utm_medium').value.trim().toLowerCase();
                    }
                    if (document.getElementById('utm_campaign').value.trim() != "") {
                        new_code.utm_campaign = document.getElementById('utm_campaign').value.trim().toLowerCase();
                    }
                    if (document.getElementById('utm_term').value.trim() != "") {
                        new_code.utm_term = document.getElementById('utm_term').value.trim().toLowerCase();
                    }
                    if (document.getElementById('utm_content').value.trim() != "") {
                        new_code.utm_content = document.getElementById('utm_content').value.trim().toLowerCase();
                    }
                    if (document.getElementById('custom_field_name').value.trim() != "" && document.getElementById('custom_field_content').value.trim() != "") {
                        new_code.custom_field = document.getElementById('custom_field_name').value.trim().toLowerCase(); 
                        new_code.custom_field_content = document.getElementById('custom_field_content').value.trim().toLowerCase();
                    }
                    urtext_tracking_codes.push(new_code);
                    showTrackingCodes();
                    document.getElementById('urtext_landing_tracking_submit_button').disabled = false;
                    closeTrackingCodePopup(event);
                }
                function openTrackingCodePopup(event) {
                    document.getElementById('tracking_code_popup_wrapper').style.display = 'flex';
                }
                function closeTrackingCodePopup(event) {
                    document.getElementById('tracking_code_popup_wrapper').style.display = 'none';
                }

                function fillRSSTypesSelect () {
                    const categoriesSelect = document.getElementById('rss_categories');
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.slug;
                        option.innerText = cat.name;
                        categoriesSelect.appendChild(option);
                    });
                    const tagsSelect = document.getElementById('rss_tags');
                    tags.forEach(tag => {
                        const option = document.createElement('option');
                        option.value = tag.slug;
                        option.innerText = tag.name;
                        tagsSelect.appendChild(option);
                    });
                    const authorsSelect = document.getElementById('rss_authors');
                    authors.forEach(author => {
                        const option = document.createElement('option');
                        option.value = author.user_nicename;
                        option.innerText = author.display_name;
                        authorsSelect.appendChild(option);
                    });
 
                }
                function selectRSSFilter(event) {
                    event.stopPropagation();
                    const container = document.getElementById('rss_feed_container');
                    let rss_base = '<?php echo esc_url( home_url( '/' ) ); ?>';

                    if (event.target.id === 'rss_categories' || (event.target.id === 'rss_tracking_codes' && document.getElementById('rss_categories').value != "")) {
                        rss_base += 'category/' + document.getElementById('rss_categories').value + '/';
                        document.getElementById('rss_tags').selectedIndex = 0;
                        document.getElementById('rss_authors').selectedIndex = 0;
                    } else if (event.target.id === 'rss_tags' || (event.target.id === 'rss_tracking_codes' && document.getElementById('rss_tags').value != "")) {
                        rss_base += 'tag/' + document.getElementById('rss_tags').value + '/';
                        document.getElementById('rss_categories').selectedIndex = 0;
                        document.getElementById('rss_authors').selectedIndex = 0;
                    } else if (event.target.id === 'rss_authors' || (event.target.id === 'rss_tracking_codes' && document.getElementById('rss_authors').value != "")) {
                        rss_base += 'author/' + document.getElementById('rss_authors').value + '/';
                        document.getElementById('rss_categories').selectedIndex = 0;
                        document.getElementById('rss_tags').selectedIndex = 0;
                    }
                    rss_base += 'feed';
                    if (document.getElementById('rss_tracking_codes').value.trim() != "") {
                        rss_base += "?urtext_code_title=" + encodeURIComponent(document.getElementById('rss_tracking_codes').options[document.getElementById('rss_tracking_codes').selectedIndex].getAttribute("title")) + "&urtext_code=" + encodeURIComponent(document.getElementById('rss_tracking_codes').value.trim());
                    }
                    container.innerHTML = rss_base;
                }
            </script>
            <h1><?php esc_html_e( 'Ur-Text Simple Landing Tracking Settings', 'urtext-landing-tracking' ); ?></h1>
            <p><?php esc_html_e( 'This plugin tracks visitor landings and provides insights into user engagement.', 'urtext-landing-tracking' ); ?></p>
            <p><?php esc_html_e( 'For more information, visit the documentation at ', 'urtext-landing-tracking' ); ?> <a href='https://urtext.ca/urtext_landing_tracking'>urtext.ca</a></p>
            <h2><?php esc_html_e( 'Tracking Codes', 'urtext-landing-tracking' ); ?>&nbsp;&nbsp;<input type="button" id='open_tracking_code_button' value="<?php esc_html_e( 'Add Tracking Code', 'urtext-landing-tracking' ); ?>"></h2>
            <div id="tracking_codes_container"></div>
                <div class='tracking_code_popup_wrapper' id='tracking_code_popup_wrapper'>
                    <div class='tracking_code_popup' id='tracking_code_popup'>
                        <h2>Add Tracking Code</h2>
                            <table class="tracking_code_table">
                                <tr>
                                    <td><lable for="utm_source"><?php esc_html_e( 'UTM Source', 'urtext-landing-tracking' ); ?>:</lable></td>
                                    <td><input type="text" id="utm_source"></td>
                                </tr>
                                <tr>
                                    <td><lable for="utm_medium"><?php esc_html_e( 'UTM Medium', 'urtext-landing-tracking' ); ?>:</lable></td>
                                    <td><input type="text" id="utm_medium"></td>
                                </tr>
                                <tr>
                                    <td><lable for="utm_campaign"><?php esc_html_e( 'UTM Campaign', 'urtext-landing-tracking' ); ?>:</lable></td>
                                    <td><input type="text" id="utm_campaign"></td>
                                </tr>
                                <tr>
                                    <td><lable for="utm_term"><?php esc_html_e( 'UTM Term', 'urtext-landing-tracking' ); ?>:</lable></td>
                                    <td><input type="text" id="utm_term"></td>
                                </tr>
                                <tr>
                                    <td><lable for="utm_content"><?php esc_html_e( 'UTM Content', 'urtext-landing-tracking' ); ?>:</lable></td>
                                    <td><input type="text" id="utm_content"></td>
                                </tr>
                                <tr><td colspan="2"><lable for="custom_field_name"><?php esc_html_e( 'Add a custom field', 'urtext-landing-tracking' ); ?></label></td></tr>
                                <tr>
                                    <td><input type="text" id="custom_field_name" placeholder="Custom field name"></td>
                                    <td><input type="text" id="custom_field_content" placeholder="Custom field content"></td>
                                </tr>
                                <tr>
                                    <td><lable for="title">Title for Code (required):</lable></td>
                                    <td><input type="text" id="title" name="title" placeholder=""></td>
                                </tr>
                            </table>
                        <button type='button' id='add_tracking_code_button' >Add Tracking Code</button>&nbsp;&nbsp;<button type='button' id='close_tracking_code_button'>Cancel</button>
                    </div>
                </div>

            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" >
                <input type="hidden" name="action" value="urtext_landing_tracking_form">
                <input type="hidden" name="urtext_landing_tracking_codes_nonce" value="<?php echo esc_attr(wp_create_nonce('urtext_landing_tracking_codes_nonce')); ?>">
                <input type="hidden" name="urtext_landing_tracking_codes" id="urtext_landing_tracking_codes" value="<?php echo esc_attr( json_encode( get_option('urtext_landing_tracking_codes', array()) ) ); ?>">
                <?php submit_button(__('Save Changes', 'urtext-landing-tracking'), 'primary', 'submit', true, array("id" => "urtext_landing_tracking_submit_button", "disabled" => true)); ?>
            <hr>
            <h2><?php esc_html_e( 'RSS Feeds With Tracking Codes', 'urtext-landing-tracking' ); ?></h2>
            <p><?php esc_html_e( 'Create RSS feed links where the URLs of the posts in the feed have tracking codes added automatically.  Use this with services like Sendible or other social media posting tools to add tracking codes to all the content you post on social media.', 'urtext-landing-tracking' ); ?></p>
            <table class='form-table'>
                <tr valign="top">
                    <td><label for="rss_categories">Category:</label></td><td><select style="width: 300px;" id="rss_categories">
                        <option value = "">Category Feed...</option></select></td>
                </tr>
                <tr valign="top">
                    <td><label for="rss_tags">Tag:</label></td><td><select style="width: 300px;" id="rss_tags">
                        <option value = "">Tag Feed...</option></select></td>
                </tr>
                <tr valign="top">
                    <td><label for="rss_authors">Author:</label></td><td><select style="width: 300px;" id="rss_authors">
                        <option value = "">Author Feed...</option></select></td>
                </tr>
                <tr valign="top">
                    <td><label for="rss_tracking_codes">Tracking Code:</label></td><td><select style="width: 300px;" id="rss_tracking_codes">
                        <option value = "">Select Tracking Code...</option></select></td>
                </tr>
            </table>
            <h3>RSS Feed URL (click to copy):</h3>
            <div id="rss_feed_container"><?php echo esc_url( home_url( '/' ) ); ?>feed</div>

            <hr>
            <h2><?php esc_html_e( 'Data Handling', 'urtext-landing-tracking' ); ?></h2>
            <p><?php esc_html_e( 'Suspend data collection?', 'urtext-landing-tracking' ); ?>&nbsp;&nbsp;&nbsp; <input type='checkbox' id='urtext_landing_tracking_suspend' name='urtext_landing_tracking_suspend' value='1' <?php checked(get_option("urtext_landing_tracking_suspend", false), 1); ?>></p>
            <p><?php esc_html_e( 'Set a maximum age to keep tracking data.  All data older than this will be deleted automatically.', 'urtext-landing-tracking' ); ?></p>
                <select name="urtext_landing_tracking_retention_days" id="urtext_landing_tracking_retention_days">
                    <option value="0" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '0' ); ?>><?php esc_html_e( 'Keep all data', 'urtext-landing-tracking' ); ?></option>
                    <option value="30" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '30' ); ?>><?php esc_html_e( '30 days', 'urtext-landing-tracking' ); ?></option>
                    <option value="60" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '60' ); ?>><?php esc_html_e( '60 days', 'urtext-landing-tracking' ); ?></option>
                    <option value="90" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '90' ); ?>><?php esc_html_e( '90 days', 'urtext-landing-tracking'); ?></option>
                    <option value="180" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '180' ); ?>><?php esc_html_e( '180 days', 'urtext-landing-tracking' ); ?></option>
                    <option value="365" <?php selected( get_option('urtext_landing_tracking_retention_days', '365'), '365' ); ?>><?php esc_html_e( '365 days', 'urtext-landing-tracking' ); ?></option>
                </select>
                <?php submit_button(__('Save Changes', 'urtext-landing-tracking'), 'primary', 'submit', true, array("id" => "urtext_retention_submit_button", "disabled" => true)); ?>
            </form>
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" onsubmit="return confirm('Are you sure you want to delete all tracking data? This action cannot be undone.');">
                <input type="hidden" name="action" value="urtext_landing_tracking_form">
                <input type="hidden" name="urtext_landing_tracking_delete_data_nonce" value="<?php echo esc_attr(wp_create_nonce('urtext_landing_tracking_delete_data_nonce')); ?>">
                <h2><?php esc_html_e( 'Delete All Tracking Data', 'urtext-landing-tracking' ); ?></h2>
                <p><?php esc_html_e( 'Permanantly delete all landing tracking data.  This process cannot be undone.', 'urtext-landing-tracking' ); ?></p>
                <?php submit_button(__('Delete All Tracking Data', 'urtext-landing-tracking'), 'delete', 'delete_tracking_data', true, array()); ?>
            </form>
            <script>
                document.getElementById('open_tracking_code_button').addEventListener('click', openTrackingCodePopup);
                document.getElementById('add_tracking_code_button').addEventListener('click', addTrackingCode);
                document.getElementById('close_tracking_code_button').addEventListener('click', closeTrackingCodePopup);
                document.getElementById('tracking_code_popup_wrapper').addEventListener('click', closeTrackingCodePopup);
                document.getElementById('tracking_code_popup').addEventListener('click', (event) => {
                    event.stopPropagation();
                });
                document.getElementById('rss_categories').addEventListener('change', selectRSSFilter);
                document.getElementById('rss_tags').addEventListener('change', selectRSSFilter);
                document.getElementById('rss_authors').addEventListener('change', selectRSSFilter);
                document.getElementById('rss_tracking_codes').addEventListener('change', selectRSSFilter);
                document.getElementById('rss_feed_container').addEventListener('click', (event) => {
                    event.stopPropagation();
                        navigator.clipboard.writeText(event.target.innerHTML.replace(/&amp;/g, '&'));
                        alert("Copied to clipboard");
                    }
                );
                document.getElementById('urtext_landing_tracking_suspend').addEventListener('change', (event) => {
                    event.stopPropagation();
                    document.getElementById('urtext_retention_submit_button').disabled = false;
                });

                document.getElementById('urtext_landing_tracking_retention_days').addEventListener('change', (event) => {
                    event.stopPropagation();
                    document.getElementById('urtext_retention_submit_button').disabled = false;
                });
                showTrackingCodes();
                fillRSSTypesSelect();
            </script>
        </div>
        <?php
    }
}