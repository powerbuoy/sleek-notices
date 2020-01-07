<?php
namespace Sleek\Notices;

#####################
# Add settings fields
add_action('admin_init', function () {
	if (get_theme_support('sleek-notice')) {
		\Sleek\Settings\add_setting('site_notice', 'textarea', esc_html__('Site notice', 'sleek'));
	}
	if (get_theme_support('sleek-cookie-consent')) {
		\Sleek\Settings\add_setting('cookie_consent', 'textarea', esc_html__('Cookie consent text', 'sleek'));
	}
	if (get_theme_support('sleek-outdated-browser-warning')) {
		\Sleek\Settings\add_setting('outdated_browser_warning', 'textarea', esc_html__('Outdated browser warning', 'sleek'));
	}
});

#####################
# Add stuff to footer
add_action('wp_footer', function () {
	# Site notice
	if (get_theme_support('sleek-notice') and ($notice = \Sleek\Settings\get_setting('site_notice'))) {
		echo '<aside id="site-notice">' . $notice . '</aside>';
	}

	# Cookie consent
	if (get_theme_support('sleek-cookie-consent')) {
		$cookieConsent = null;

		if ($consent = \Sleek\Settings\get_setting('cookie_consent')) {
			$cookieConsent = $consent;
		}
		else {
			$cookieUrl = (get_option('wp_page_for_privacy_policy') and get_post_status(get_option('wp_page_for_privacy_policy')) === 'publish') ? get_permalink(get_option('wp_page_for_privacy_policy')) : 'https://cookiesandyou.com/';
			$cookieConsent = apply_filters('sleek_cookie_consent', sprintf(__('We use cookies to bring you the best possible experience when browsing our site. <a href="%s" target="_blank">Read more</a> | <a href="#" class="close">Accept</a>', 'sleek'), $cookieUrl), $cookieUrl);
		}

		# Add cookie consent with JS
		if ($cookieConsent) {
			?>
			<script>
				var accept = window.localStorage.getItem('sleek_cookie_consent');

				if (!accept) {
					var el = document.createElement('aside');

					el.id = 'cookie-consent';
					el.innerHTML = '<?php echo addslashes($cookieConsent) ?>';

					document.body.appendChild(el);

					var close = el.querySelector('a.close');

					if (close) {
						close.addEventListener('click', function (e) {
							e.preventDefault();
							window.localStorage.setItem('sleek_cookie_consent', true);
							el.parentNode.removeChild(el);
						});
					}
				}
			</script>
			<?php
		}
	}

	# IE warning
	if (get_theme_support('sleek-outdated-browser-warning')) {
		$browserWarning = null;

		if ($warning = \Sleek\Settings\get_setting('outdated_browser_warning')) {
			$browserWarning = $warning;
		}
		else {
			$browserWarning = apply_filters('sleek_outdated_browser_warning', __('<strong>Oops!</strong> Your browser is not supported. For a richer browsing experience, please consider upgrading to a better, modern browser like <a href="https://www.google.com/chrome/">Google Chrome</a>, <a href="https://www.mozilla.org/en-US/firefox/new/">Mozilla Firefox</a>, <a href="https://support.apple.com/downloads/safari">Safari</a>, <a href="https://www.opera.com/">Opera</a> or <a href="https://www.microsoft.com/en-us/windows/microsoft-edge">Microsoft Edge</a>.', 'sleek'));
		}

		if ($browserWarning and (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE ') or strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/'))) {
			echo '<aside id="outdated-browser-warning">' . $browserWarning . '</aside>';
		}
	}
});
