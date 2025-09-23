<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://urtext.ca
 * @since      1.0.0
 *
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/admin
 */

class Urtext_Landing_Tracking_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if (version_compare(get_option( 'urtext_landing_tracking_db_version', '0.0.0' ), URTEXT_LANDING_TRACKING_DB_VERSION, "<")) {
			require_once plugin_dir_path( __FILE__ ) . '../includes/class-urtext-landing-tracking-activator.php';
			Urtext_Landing_Tracking_Activator::update_database();
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/urtext-landing-tracking-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/urtext-landing-tracking-admin.js', array( 'jquery' ), $this->version, false );
		if ($hook == "landing-tracking_page_urtext_landing_tracking_menu_reports_slug") {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chart.js/chart.umd.min.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Register the Admin menus.
	 *
	 * @since    1.0.0
	 */

	public function update_menu() {
		global $menu, $submenu;
		add_menu_page( "Ur-Text Landing Tracking", "Landing Tracking", "manage_options", 'urtext_landing_tracking_menu_slug', array( $this, 'show_settings_page' ), 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgoKPHN2ZwogICB3aWR0aD0iMjkuODQ1MDAxbW0iCiAgIGhlaWdodD0iMjkuODQ1MDAxbW0iCiAgIHZpZXdCb3g9IjAgMCAyOS44NDUwMDEgMjkuODQ1MDAxIgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmc1IgogICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZGVmcwogICAgIGlkPSJkZWZzMiIgLz4KICA8ZwogICAgIGlkPSJsYXllcjEiCiAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTcyLjY2NzU2NCwtOTUuMjc1MjkxKSI+CiAgICA8cGF0aAogICAgICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtzdHJva2Utd2lkdGg6MC4wNDIzMzMzIgogICAgICAgZD0ibSA4Ni4xODc1MywxMjQuMDQwNjUgYyAtMS40Mjk0NDcsLTAuMTgxMSAtMi43MDE4OCwtMC41NzIxOSAtMy45NzM3NjMsLTEuMjIxMzcgLTAuNTY4NDI4LC0wLjI5MDE0IC0xLjU0MDg4OCwtMC45MDM2NiAtMS44MzIxODUsLTEuMTU1OTQgbCAtMC4xMzg4MTcsLTAuMTIwMjEgMC40ODgwNjcsLTAuNjA4NjggYyAwLjI2ODQzNiwtMC4zMzQ3NyAwLjQ4NzM0NCwtMC42MjA1MiAwLjQ4NjQ2MywtMC42MzUgLTguODFlLTQsLTAuMDE0NSAtMC4wODc5MSwtMC4wOTMgLTAuMTkzNDAzLC0wLjE3NDQ5IC0wLjEwNTQ5MSwtMC4wODE1IC0wLjQzNzA1MSwtMC4zOTk4NCAtMC43MzY4LC0wLjcwNzQzIGwgLTAuNTQ1LC0wLjU1OTI2IC0wLjA4ODc3LDAuMTQzNjMgYyAtMC4xMDY4NDMsMC4xNzI4NyAtMC40MjQyODEsMC4zOTE2NSAtMC42MjIxMjgsMC40Mjg3NyAtMC4yNzM3OTMsMC4wNTE0IC0wLjYyMTY3OCwtMC4wMDIgLTAuODM1OTU2LC0wLjEyNzExIC0wLjQ3ODY4MiwtMC4yODA1MyAtMS40Njc4NzEsLTIuMTM0OTEgLTEuODgxNDQxLC0zLjUyNzA0IC0wLjE2NTA4OSwtMC41NTU3MSAtMC4zMjM4MDEsLTEuMjczMTcgLTAuNDE0Njc4LC0xLjg3NDU2IC0wLjA2NzY0LC0wLjQ0NzY1IC0wLjA3NDY5LC0xLjE3NTk3IC0wLjA4NzUsLTkuMDQ4NzUgbCAtMC4wMTM5NCwtOC41NjE5MTkgaCAyLjY4OTQ0MSBjIDIuMzIwMjk5LDAgMi42ODk0NDEsMC4wMDg0IDIuNjg5NDQxLDAuMDYxMzQgMCwwLjA1MzU2IDAuMDE4NjEsMC4wNTM1NiAwLjE0NjgwNywwIDAuMTc2ODI1LC0wLjA3Mzg4IDAuNTQ5MzYsLTAuMDc5ODkgMC43NDQxMjEsLTAuMDExOTkgMC4xMDc3OTEsMC4wMzc1OCAwLjE0NTQ4NiwwLjAzNzU4IDAuMTU4MDExLDAgMC4wMTE5NSwtMC4wMzU4NSAwLjMxMDc2NCwtMC4wNDkzNSAxLjA5MjQyMiwtMC4wNDkzNSBoIDEuMDc1OTczIGwgLTkuMzJlLTQsMy44NjI5MTkgYyAtOS4xNGUtNCwzLjc5MzE1IC0wLjAwMjUsMy44NjYzNiAtMC4wODcwNiw0LjA1MzQxIC0wLjI1NjE1NywwLjU2NjU1IC0wLjg0OTA5NiwwLjc5ODk5IC0xLjQ0OTk3NiwwLjU2ODQxIC0wLjA2NTQ4LC0wLjAyNTEgLTAuMDcwNywwLjEzNDEzIC0wLjA3MDcsMi4xNTc1NyB2IDIuMTg0NjkgaCAwLjgwMTIyNiAwLjgwMTIyNSBsIDAuMDE2NjUsMS44NzMyNSBjIDAuMDE1OTUsMS43OTQxMSAwLjAyMDY3LDEuODg2NjcgMC4xMTE4MzcsMi4xOTA3NSAwLjE2MjE1NiwwLjU0MDg4IDAuNDA2MzkxLDAuOTUxMDEgMC44MDkwNiwxLjM1ODYyIDAuNTYyNTk3LDAuNTY5NSAxLjE2MzA4MiwwLjg2OTA5IDEuOTM1ODk5LDAuOTY1ODQgMC4zOTE4NDQsMC4wNDkxIDAuNjA1OTA0LDAuMTQyNSAwLjgwMDQ0NSwwLjM0OTQyIDAuMjYzMjY3LDAuMjgwMDEgMC4zNzA5MDUsMC42ODE5NSAwLjI3NTMyMywxLjAyODEyIC0wLjAyNTMyLDAuMDkxNyAtMC4wMzgzNCwwLjE3NDQ0IC0wLjAyODk0LDAuMTgzODQgMC4wMDk0LDAuMDA5IDAuMjEzNjc2LC0wLjAzMjEgMC40NTM5MzYsLTAuMDkyMyAwLjU3NDU4NywtMC4xNDM5MyAxLjMzNzIxNSwtMC41MjIyMSAxLjc5ODg0OSwtMC44OTIyNiAwLjMzMzQzOSwtMC4yNjcyOSAwLjgxOTc0OSwtMC43NzUxMiAwLjgxNjY1NSwtMC44NTI3OSAtOC4yNmUtNCwtMC4wMjA3IC0wLjI1OTI5NiwtMC4yMjgxNCAtMC41NzQzOCwtMC40NjA5NyAtMC4zMTUwODMsLTAuMjMyODMgLTAuNTkyNzcsLTAuNDQ2OTEgLTAuNjE3MDgyLC0wLjQ3NTcyIC0wLjAzMTk3LC0wLjAzNzkgLTAuMDA4NCwtMC4xMDgyMyAwLjA4NTI2LC0wLjI1NDE0IDAuMTYyMjY4LC0wLjI1Mjg3IDAuMzE5NDczLC0wLjYwNjEzIDAuNDEyNjk1LC0wLjkyNzM4IDAuMDY1MDUsLTAuMjI0MTYgMC4wNzM0NywtMC42NjA5OSAwLjA4ODQsLTQuNTg2OTMgMC4wMTgxNCwtNC43NzA3MSAwLjAwNTEsLTQuNTQxMzIgMC4yNzYxMDUsLTQuODQ5OTkgMC4zMTE0OTQsLTAuMzU0NzcgMC44MTIzMjYsLTAuNDY5NjcgMS4yNzI0MjIsLTAuMjkxOSAwLjA2OTM5LDAuMDI2OCAwLjA3NDA4LC0wLjA0MTMgMC4wNzQwOCwtMS4wNzY1OCB2IC0xLjEwNTE5IGggLTAuODA0MzMzIC0wLjgwNDMzNCB2IC0yLjQ1NTMzNiAtMi40NTUzMzMgaCAxLjA3NTk3MyBjIDAuNzgxNjU4LDAgMS4wODA0NzEsMC4wMTM1IDEuMDkyNDIyLDAuMDQ5MzUgMC4wMTI1MiwwLjAzNzU4IDAuMDUwMjIsMC4wMzc1OCAwLjE1ODAxMSwwIDAuMTk0NzYxLC0wLjA2Nzg5IDAuNTY3Mjk2LC0wLjA2MTg5IDAuNzQ0MTIxLDAuMDExOTkgMC4xMjgxOTcsMC4wNTM1NiAwLjE0NjgwNywwLjA1MzU2IDAuMTQ2ODA3LDAgMCwtMC4wNTE5NiAwLjE2NTA2MiwtMC4wNjEzNCAxLjA3OTUsLTAuMDYxMzQgaCAxLjA3OTUgdiAwLjk3MTUwNyAwLjk3MTUwNiBsIDAuMTU4NjI5LC0wLjA2NjI4IGMgMC4xMTIwODIsLTAuMDQ2ODMgMC4yNDE1ODQsLTAuMDYwMzQgMC40NDEzMzIsLTAuMDQ2MDUgMC4zMzI0NjksMC4wMjM3OSAwLjU5MzU5NSwwLjE1NjYwNSAwLjc3NDY4NywwLjM5NDAyOCAwLjIxNDU0OCwwLjI4MTI4NyAwLjIzNDAxOSwwLjQyOTU5NiAwLjIzNDAxOSwxLjc4MjUxOCB2IDEuMjQyMSBoIDAuODA3MzIgMC44MDczMiBsIC0wLjAxNzYyLDUuOTM3MjUgYyAtMC4wMTczMyw1LjgzODYzIC0wLjAxOTE1LDUuOTQ3MSAtMC4xMDk1Nyw2LjUyOTkyIC0wLjM2MzM0MSwyLjM0MjA0IC0xLjI3NTE5NSw0LjMyMzI2IC0yLjgwNzQ5MSw2LjA5OTkzIC0wLjc2ODkwMSwwLjg5MTUzIC0xLjkxMjc2MSwxLjgzOTYyIC0yLjg3MTY2OSwyLjM4MDE5IC0wLjQ4MzU3LDAuMjcyNiAtMC45ODM1NjEsMC4yMTI4MSAtMS4zNTg4ODMsLTAuMTYyNTEgLTAuMTgzNzkxLC0wLjE4MzggLTAuMzMzNzM4LC0wLjQ5OTQ4IC0wLjMzMzczOCwtMC43MDI2NCAwLC0wLjAzNTUgLTAuMDg4MDcsLTAuMDEzMiAtMC4yNjUzNTYsMC4wNjcyIC0wLjQwOTY3OCwwLjE4NTc2IC0xLjA2MTA5NywwLjQwMTQzIC0xLjU5NzMxMSwwLjUyODg1IC0wLjY0NTQ1NCwwLjE1MzM3IC0wLjYwNDk5NiwwLjEzNjIzIC0wLjU3OTYwNywwLjI0NTUgMC4wNzkwMiwwLjM0MDA3IDAuMjUyMjQ2LDEuNDMxMTYgMC4yMzA2NDgsMS40NTI3NiAtMC4xNDUyMzIsMC4xNDUyMyAtMi42MjM3NCwwLjIzMzA4IC0zLjQ4Nzc0MiwwLjEyMzYyIHogbSAxLjU1MDcwMSwtMi42MDMzNiB2IC0wLjUwOCBsIC0wLjExNjQxNiwtOS42ZS00IGMgLTAuMjAxMDgsLTAuMDAyIC0xLjE2MjgzNCwtMC4wODE2IC0xLjMwNjk0LC0wLjEwODY0IGwgLTAuMTM3Njg2LC0wLjAyNTggdiAwLjI3Nzg4IGMgMCwwLjI0OTg3IC0wLjA1MTEzLDAuNDI1OTggLTAuMjA2MDc4LDAuNzA5ODEgLTAuMDA4LDAuMDE0NyAwLjE3MTk2OCwwLjA0OCAwLjQwMDAwNiwwLjA3MzkgMC4yMjgwMzgsMC4wMjU5IDAuNDcxNzY0LDAuMDUzOSAwLjU0MTYxNCwwLjA2MjIgMC4wNjk4NSwwLjAwOCAwLjI4NDE2MywwLjAxNzkgMC40NzYyNSwwLjAyMTQgbCAwLjM0OTI1LDAuMDA2IHogbSAzLjM1NjEyNCwtMS45MDk3MSBjIDAuMjEwNDU5LC0wLjEwNTI1IDAuNzE1OTM4LC0wLjM5MDcgMC43ODYwNTIsLTAuNDQzODkgMC4wMDgxLC0wLjAwNiAtMC4xMDUwNzUsLTAuMTk4MjEgLTAuMjUxNDM3LC0wLjQyNjg1IC0wLjE0NjM2MSwtMC4yMjg2NCAtMC4yNjg4MjUsLTAuNDIwNDUgLTAuMjcyMTQxLC0wLjQyNjI0IC0wLjAwMzMsLTAuMDA2IC0wLjE1MzAwNywwLjA3MzIgLTAuMzMyNjQ1LDAuMTc1NjEgLTAuMTc5NjM5LDAuMTAyMzcgLTAuMzgzMjQ2LDAuMjA5NTkgLTAuNDUyNDU5LDAuMjM4MjYgbCAtMC4xMjU4NDMsMC4wNTIxIDAuMTA4MzE0LDAuMTQyMDEgYyAwLjEzODA0MSwwLjE4MDk4IDAuMjMyMDM1LDAuNDQ2OTkgMC4yMzIwMzUsMC42NTY2OCAwLDAuMDkwMiAwLjAxMDA3LDAuMTY0MDEgMC4wMjIzNywwLjE2NDAxIDAuMDEyMzEsMCAwLjE0MDg5MywtMC4wNTkzIDAuMjg1NzUsLTAuMTMxNzEgeiBtIC04LjQwODkwMiwtNC4yMzIxIGMgMC4xMTg4OTYsLTAuMDYyMSAwLjIzNzUxOCwtMC4xMjAwOCAwLjI2MzYwNCwtMC4xMjg4MSAwLjAyODk0LC0wLjAxIC0wLjAyNzIxLC0wLjE2NDQgLTAuMTQ0MDU0LC0wLjM5Njg4IC0wLjMzNzA1MywtMC42NzA2NCAtMC41NzAxMDUsLTEuNTU1NDggLTAuNTcwMTA1LC0yLjE2NDU0IHYgLTAuMjY5NjMgaCAtMC41MzAzNDYgLTAuNTMwMzQ2IGwgMC4wMjQxOCwwLjI4NTc1IGMgMC4wOTY2LDEuMTQxNzQgMC4zNjk5ODcsMi4wODYyMiAwLjgyNjEwNSwyLjg1Mzk4IGwgMC4xNzI0LDAuMjkwMTkgMC4xMzYxOTUsLTAuMTc4NTcgYyAwLjA3NTAxLC0wLjA5ODMgMC4yMzMzMDcsLTAuMjI5MjkgMC4zNTIzNywtMC4yOTE0OSB6IG0gMTQuNDkyNDY0LC0zLjIyNDQ0IDYuNDhlLTQsLTEuMDkwMDggaCAtMC40ODY4MzQgLTAuNDg2ODMzIGwgLTUuNzZlLTQsMC45NjMwOCBjIC0zLjM4ZS00LDAuNTI5NyAtMC4wMTEwMywxLjAwMTk4IC0wLjAyMzgsMS4wNDk1MiAtMC4wMjE4NiwwLjA4MTQgLTAuMDA1MSwwLjA4NjMgMC4yODYzMjgsMC4wODQ3IDAuMjI4MTQsLTAuMDAxIDAuMzU0MDgxLDAuMDIwNiAwLjQ3ODg4MiwwLjA4MzMgMC4xMDgzMDIsMC4wNTQ0IDAuMTgwNTQzLDAuMDY5NiAwLjIwMDQzNSwwLjA0MjMgMC4wMTcxMSwtMC4wMjM1IDAuMDMxMzksLTAuNTMzMjQgMC4wMzE3NSwtMS4xMzI3OSB6IG0gLTMuMTgwMTY2LC0zLjIxNzMzIDAuMDA1OCwtMS41NTU3NSBoIC0wLjUxODU4NCBsIC0wLjUxODU4MywtMWUtNSB2IDIuNjgxMjkgMi42ODEyOSBsIDAuMTY1MiwtMC4wMzcxIGMgMC4xODAzMjcsLTAuMDQwNSAwLjUyMzg2MywwLjAxNDcgMC43MDI2MzMsMC4xMTMwMiBsIDAuMTA1ODM0LDAuMDU4MiAwLjAyNTk0LC0xLjE5MjU4IGMgMC4wMTQyNiwtMC42NTU5MSAwLjAyODU1LC0xLjg5MjY2IDAuMDMxNzUsLTIuNzQ4MzIgeiBtIC0xMy42MzE3MywzLjQ1NDcyIDAuMjYwMjEsLTAuMDI3MiB2IC0xLjYyODMzIGMgMCwtMS41MzQzMyAtMC4wMDQzLC0xLjYyNjU1IC0wLjA3NDA4LC0xLjU5NzQ3IC0wLjI4MzU5NywwLjExODE1IC0wLjYwMTExOSwwLjExNzM5IC0wLjkxMDE2NywtMC4wMDIgLTAuMDYzNTcsLTAuMDI0NiAtMC4wNzQxNCwtMC4wMDIgLTAuMDc0NSwwLjE1NTc4IC0wLjAwMTUsMC42NjkxNyAtMC43NTk1MzQsMS4xODA3IC0xLjQwNzE3LDAuOTQ5NTggbCAtMC4xNTg3NSwtMC4wNTY3IHYgMC41NjY1MSAwLjU2NjUxIGggMC44MDQzMzQgMC44MDQzMzMgdiAwLjU1MDMzIDAuNTUwMzQgaCAwLjI0Nzc5IGMgMC4xMzYyODUsMCAwLjM2NDg4NSwtMC4wMTIyIDAuNTA4LC0wLjAyNzIgeiBNIDk3LjE3ODU2NSw5Ny4wNTMyOTEgdiAtMC43NjIgaCAxLjEwMDY2NyAxLjEwMDY2NiB2IDAuNzYyIDAuNzYyIGggLTEuMTAwNjY2IC0xLjEwMDY2NyB6IgogICAgICAgaWQ9InBhdGgxMDUwIiAvPgogIDwvZz4KPC9zdmc+Cg==' );
		add_submenu_page(
			'urtext_landing_tracking_menu_slug',
			"Ur-Text Landing Tracking Settings",
			'Settings',
			'manage_options',
			'urtext_landing_tracking_menu_slug',
			array( $this, 'show_settings_page' )
		);
		add_submenu_page(
			'urtext_landing_tracking_menu_slug',
			"Ur-Text Landing Tracking Reports",
			'Reports',
			'manage_options',
			'urtext_landing_tracking_menu_reports_slug',
			array( $this, 'show_reports_page' )
		);
	}

	/**
	 * Add a button to the wpadmin bar to generate a tracking url for the current page
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function add_tracking_url_button() {
		if (! is_singular() && ! is_home()) {
			return;
		}
		global $wp_admin_bar;
		$args = array( 
			'id' => 'urtext_landing_tracking_url_button',
			'title' => 'Tracking URLs',
		);
		$wp_admin_bar->add_node( $args );
		foreach (get_option("urtext_landing_tracking_codes", array()) as $code_entry) {
			$codes = array();
			foreach ($code_entry as $key => $value) {
				if ($key == "date_added" || $key == "title" || $key == "custom_field_content") {
					continue;
				}
				if ($key == "custom_field" ) {
					$codes[$value] =  urlencode($code_entry["custom_field_content"]);
				} else {
					$codes[$key] = urlencode($value);
				}
			}
			$tracking_url = add_query_arg( $codes, get_permalink() );
			$tracking_url_display = $tracking_url;
			if (strlen($tracking_url) > 50) {
				$tracking_url_display = substr($tracking_url, 0, 35) . "..." . substr($tracking_url, -35);
			}
			$wp_admin_bar->add_node( array(
				'id' => 'urtext_landing_tracking_url_' . md5($tracking_url),
				'title' => esc_html( $code_entry['title'] ) . ' (' . esc_html( $tracking_url_display ) . ')',
				'parent' => 'urtext_landing_tracking_url_button',
				'meta' => array(
					'target' => '_blank',
					'class' => 'urtext-landing-tracking-url',
					'onclick' => 'event.stopPropagation();navigator.clipboard.writeText("' . $tracking_url . '"); alert("Tracking URL copied to clipboard");',
				),
			) );
		}
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting( 
			'urtext_landing_tracking_settings_group', 
			'urtext_landing_tracking_codes',
			array(
				'type' => 'array',
				'description' => 'Tracking Codes',
				'sanitize_callback' => array($this,'sanitize_tracking_codes'),
				'show_in_rest' => false,
				'default'=> array(),
			)
		);
		register_setting( 
			'urtext_landing_tracking_settings_group', 
			'urtext_landing_tracking_suspend',
			array(
				'type' => 'boolean',
				'description' => 'Suspend Tracking',
				'sanitize_callback' => array($this,'sanitize_tracking_suspend'),
				'show_in_rest' => false,
				'default'=> false,
			)
		);
		register_setting( 
			'urtext_landing_tracking_settings_group', 
			'urtext_landing_tracking_retention_days',
			array(
				'type' => 'integer',
				'description' => 'Tracking Data Retention Period (days)',
				'sanitize_callback' => array($this,'sanitize_tracking_retention_days'),
				'show_in_rest' => false,
				'default'=> 365,
			)
		);
	}

	/**
	 * Sanitize the tracking codes.
	 * 
	 * @since    1.0.0
	 */
	public function sanitize_tracking_codes($array) {
		$tracking_codes = array();
		$custom_tracking_fields = array();
		foreach ($array as $code_entry) {
			$new_code = array();
			foreach ($code_entry as $key => $value) {
				$new_code[strtolower($key)] = sanitize_text_field($value);
				if ($key == "custom_field" ) {
					// Custom tracking code field
					$custom_tracking_fields[strtolower($value)] = true;
				}
			}
			if (!empty($new_code)) {
				$tracking_codes[] = $new_code; 
			}
		}
		update_option("urtext_landing_tracking_custom_fields", array_keys($custom_tracking_fields), false);
		return $tracking_codes;
	}

	/**
	 * Sanitize the suspend return days.
	 * 
	 * @since    1.0.0
	 */
	public function sanitize_tracking_suspend($input) {

		return (bool)$input;
	}

	/**
	 * Sanitize the retention days.
	 * 
	 * @since    1.0.0
	 */
	public function sanitize_tracking_retention_days($input) {
		return abs(intval($input));
	}

	/**
	 * Show the settings page.
	 *
	 * @since    1.0.0
	 */
	public function show_settings_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/urtext-landing-tracking-admin-display-settings.php';
		$urtext_landing_tracking_admin_display = new Urtext_Landing_Tracking_Admin_Display_Settings();
		$urtext_landing_tracking_admin_display->show_settings_page();
	}

	/**
	 * Show the report page.
	 *
	 * @since    1.0.0
	 */
	public function show_reports_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/urtext-landing-tracking-admin-display-report.php';
		$urtext_landing_tracking_admin_display = new Urtext_Landing_Tracking_Admin_Display_Report();
		$urtext_landing_tracking_admin_display->show_reports_page();
	}

	/**
	 * Process the setting page forms
	 *
	 * @since    1.0.0
	 */
	public function process_form() {
		global $wpdb;
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(esc_html_e( 'You do not have sufficient permissions to access this page.', 'urtext-landing-tracking' ) );
		}

		if (isset($_POST["urtext_landing_tracking_codes_nonce"]) && check_admin_referer( 'urtext_landing_tracking_codes_nonce', 'urtext_landing_tracking_codes_nonce' ) ) {
			// Setting page form that updates tracking codes, data retention time and
			// tracking suspension toggle
			if (isset($_POST['urtext_landing_tracking_codes'])) {
				$tracking_codes = json_decode(sanitize_text_field(wp_unslash($_POST['urtext_landing_tracking_codes'])), true);
				update_option("urtext_landing_tracking_codes", $tracking_codes, false);
			}
			if (isset($_POST['urtext_landing_tracking_suspend'])) {
				update_option("urtext_landing_tracking_suspend", 1, true);
			} else {
				update_option("urtext_landing_tracking_suspend", 0, true);
			}

			if (isset($_POST['urtext_landing_tracking_retention_days'])) {
				update_option("urtext_landing_tracking_retention_days", intval($_POST['urtext_landing_tracking_retention_days']), false);
			}

			wp_redirect( admin_url( 'admin.php?page=urtext_landing_tracking_menu_slug&updated=true' ) );
			exit;
		} else if (isset($_POST["urtext_landing_tracking_delete_data_nonce"]) && check_admin_referer( 'urtext_landing_tracking_delete_data_nonce', 'urtext_landing_tracking_delete_data_nonce' ) ) {
			// Settnig page form that is just for deleting all the tracking data
			$table_name = $wpdb->prefix . 'urtext_landing_tracking_sessions';
			$wpdb->get_results($wpdb->prepare("DELETE FROM %s", $table_name)); //db call ok; no-cache ok
			wp_redirect( admin_url( 'admin.php?page=urtext_landing_tracking_menu_slug&deleted=true' ) );
			exit;
		}
		wp_die( esc_html_e( 'Invalid request.', 'urtext-landing-tracking' ) );	
	}
}
