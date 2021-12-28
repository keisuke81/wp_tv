<?php

class Alti_ProtectUploads_Admin
{

	private $plugin_name;
	private $version;
	private $messages = array();

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	public function add_submenu_page()
	{
		add_submenu_page('upload.php', $this->plugin_name, 'Protect Uploads <span class="dashicons dashicons-shield-alt" style="font-size:15px;"></span>', 'manage_options', $this->plugin_name . '-settings-page', array($this, 'render_settings_page'));
	}

	public function render_settings_page()
	{
		require plugin_dir_path(__FILE__) . 'views/' . $this->plugin_name . '-admin-settings-page.php';
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/css/protect-uploads-admin.css', array(), $this->version, 'all');
	}

	public function add_settings_link($links)
	{
		$settings_link = '<a href="upload.php?page=' . $this->plugin_name . '-settings-page">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	public function get_uploads_dir()
	{
		$uploads_dir = wp_upload_dir();
		return $uploads_dir['basedir'];
	}

	public function get_uploads_url()
	{
		$uploads_dir = wp_upload_dir();
		return $uploads_dir['baseurl'];
	}

	public function get_uploads_subdirectories()
	{

		$directories = scandir(self::get_uploads_dir());
		$subs = array(self::get_uploads_dir());

		foreach ($directories as $directory) {

			if (is_dir(self::get_uploads_dir() . '/' . $directory) && !preg_match('/^\.*$/', $directory)) {
				$subs[] = self::get_uploads_dir() . '/' . $directory;
				$subDirectories = scandir(self::get_uploads_dir() . '/' . $directory);
				foreach ($subDirectories as $subDirectory) {
					if (is_dir(self::get_uploads_dir() . '/' . $directory . '/' . $subDirectory) && !preg_match('/^\.*$/', $subDirectory)) $subs[] = self::get_uploads_dir() . '/' . $directory . '/' . $subDirectory;
				}
			}
		}
		return $subs;
	}

	public function save_form($form)
	{
		if ($form['protection'] == 'index_php') {
			$this->create_index();
		}
		if ($form['protection'] == 'htaccess') {
			$this->create_htaccess();
		}
		if ($form['protection'] == 'remove') {
			$this->remove_index();
			$this->remove_htaccess();
		}
	}

	// used to check if the current htaccess has been generated by the plugin
	public function get_htaccess_identifier()
	{
		return "[plugin_name=" . $this->plugin_name . "]";
	}

	public function create_index()
	{
		// check if index php does not exists
		if (self::check_protective_file('index.php') === false) {

			$indexContent = "<?php // Silence is golden \n // " . self::get_htaccess_identifier() . " \n // https://www.alticreation.com/en/protect-uploads/ \n // date:" . date('d/m/Y') . "\n // .";
			$i = 0;
			foreach (self::get_uploads_subdirectories() as $subDirectory) {

				if (!file_put_contents($subDirectory . '/' . 'index.php', $indexContent)) {
					self::register_message('Impossible to create or modified the index.php file in ' . $subDirectory, 'error');
				} else {
					$i++;
				}
			}

			if ($i == count(self::get_uploads_subdirectories())) {
				self::register_message('The index.php file has been created in main folder and subfolders (two levels max).');
			}
		}
		// if index php already exists
		else {
			self::register_message('The index.php file already exists', 'error');
		}
	}

	public function create_htaccess()
	{
		// Content for htaccess file
		$date             = date('Y-m-d H:i.s');
		$phpv             = phpversion();

		$htaccessContent  = "\n# BEGIN " . $this->get_plugin_name() . " Plugin\n";
		$htaccessContent  .= "\tOptions -Indexes\n";
		$htaccessContent  .= "# [date={$date}] [php={$phpv}] " . self::get_htaccess_identifier() . " [version={$this->version}]\n";
		$htaccessContent  .= "# END " . $this->get_plugin_name() . " Plugin\n";

		// if htaccess does NOT exist yet
		if (self::check_protective_file('.htaccess') === false) {
			// try to create and save the new htaccess file
			if (!file_put_contents(self::get_uploads_dir() . '/' . '.htaccess', $htaccessContent)) {
				self::register_message('Impossible to create or modified the htaccess file.', 'error');
			} else {
				self::register_message('The htaccess file has been created.');
			}
		}
		else {
			// if content added to existing htaccess
			if (file_put_contents(self::get_uploads_dir() . '/.htaccess', $htaccessContent, FILE_APPEND | LOCK_EX)) {
				self::register_message('The htaccess file has been updated.');
			} else {
				self::register_message('The existing htaccess file couldn\'t be updated. Please check file permissions.', 'error');
			}
		}
	}

	public function remove_index()
	{
		$i = 0;
		foreach (self::get_uploads_subdirectories() as $subDirectory) {
			if (file_exists($subDirectory . '/index.php')) {
				unlink($subDirectory . '/index.php');
				$i++;
			}
		}
		if ($i == count(self::get_uploads_subdirectories())) {
			self::register_message('The index.php file(s) have(has) been deleted.');
		}
	}

	public function remove_htaccess()
	{
		if (file_exists(self::get_uploads_dir() . '/.htaccess')) {

			$htaccessContent = file_get_contents(self::get_uploads_dir() . '/.htaccess');
			$htaccessContent = preg_replace('/(# BEGIN protect-uploads Plugin)(.*?)(# END protect-uploads Plugin)/is', '', $htaccessContent);
			file_put_contents(self::get_uploads_dir() . '/.htaccess', $htaccessContent, LOCK_EX);

			// if htaccess is empty, we remove it.
			if (strlen(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", file_get_contents(self::get_uploads_dir() . '/.htaccess'))) == 0) {
				unlink(self::get_uploads_dir() . '/.htaccess');
			}


			//
			self::register_message('The htaccess file has been updated.');
		}
	}

	public function get_protective_files_array()
	{
		$uploads_files = ['index.php', 'index.html', '.htaccess'];
		$response = [];
		foreach ($uploads_files as $file) {
			if (file_exists(self::get_uploads_dir() . '/' . $file)) {
				$response[] = $file;
			}
		}
		return $response;
	}

	public function check_protective_file($file)
	{
		if (in_array($file, self::get_protective_files_array())) {
			return true;
		} else {
			return false;
		}
	}

	public function get_uploads_root_response_code()
	{
		$uploads_headers = get_headers(self::get_uploads_url() . '/');
		$response = null;
		if (is_array($uploads_headers)) {
			if (preg_match('/200/', $uploads_headers[0])) $response = 200;
			if (preg_match('/403/', $uploads_headers[0])) $response = 403;
		}
		return $response;
	}

	public function get_htaccess_content()
	{
		return file_get_contents(self::get_uploads_dir() . '/.htaccess');
	}

	public function check_htaccess_is_self_generated()
	{
		if (self::check_protective_file('.htaccess') && preg_match('/' . self::get_htaccess_identifier() . '/', self::get_htaccess_content())) {
			return true;
		} else {
			return false;
		}
	}

	// heart? <3
	public function check_uploads_is_protected()
	{
		foreach (self::get_protective_files_array() as $file) {
			if ($file === 'index.html') {
				return true;
				break;
			}
			if ($file === 'index.php') {
				return true;
				break;
			}
			if ($file === '.htaccess' && self::get_uploads_root_response_code() === 200) {
					return false;
					break;
			}
		}
		if (self::get_uploads_root_response_code() === 403) {
			return true;
		}
		else {
			return false;
		}
	}

	public function check_protective_file_removable() {
		if( self::check_protective_file('index.html') ) {
			return false;
		}
		elseif( self::check_protective_file('.htaccess') === false && self::get_uploads_root_response_code() === 403 ) {
			return false;
		}
		else {
			return true;
		}
	}

	public function get_uploads_protection_message_array()
	{
		$response = [];
		foreach (self::get_protective_files_array() as $file) {
			if ($file === '.htaccess' && self::get_uploads_root_response_code() === 403) {
				$response[] = '<span class="dashicons dashicons-yes"></span> ' . __('.htaccess file is present and access to uploads directory returns 403 code.', $this->plugin_name);
			}
			if ($file === 'index.php') {
				$response[] = '<span class="dashicons dashicons-yes"></span> ' . __('index.php file is present.', $this->plugin_name);
			}
			if ($file === 'index.html') {
				$response[] = '<span class="dashicons dashicons-yes"></span> ' . __('index.html file is present.', $this->plugin_name);
			}
		}
		if (self::check_protective_file('.htaccess') === true && self::get_uploads_root_response_code() === 200) {
			$response[] = '<span class="dashicons dashicons-search"></span> ' . __('.htaccess file is present but not protecting uploads directory.', $this->plugin_name);
		}
		if (self::check_protective_file('.htaccess') === false && self::get_uploads_root_response_code() === 403) {
			$response[] = '<span class="dashicons dashicons-yes"></span> ' . __('Access to uploads directory is protected (403) with a global .htaccess or another global declaration.', $this->plugin_name);
		}
		return $response;
	}

	public function check_apache()
	{

		if (!function_exists('apache_get_modules')) {
			self::register_message('The Protect Uploads plugin cannot work without Apache. Yourself or your web host has to activate this module.');
		}
	}


	public function register_message($message, $type = 'updated', $id = 0)
	{
		$this->messages['apache'][] = array(
			'message' => __($message, $this->plugin_name),
			'type' => $type,
			'id' => $id
		);
	}

	public function display_messages()
	{

		foreach ($this->messages as $name => $messages) {
			foreach ($messages as $message) {
				return '<div id="message" class="' . $message['type'] . '"><p>' . $message['message'] . '</p></div>';
			}
		}
	}
}
