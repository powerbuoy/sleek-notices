<?php
namespace Sleek\Notices;

#####################
# Add settings fields
add_action('admin_init', function () {
	if (get_theme_support('sleek/notices/cookie_consent')) {
		\Sleek\Settings\add_setting('cookie_consent', 'textarea', esc_html__('Cookie consent text', 'sleek_admin'));
	}
	if (get_theme_support('sleek/notices/outdated_browser_warning')) {
		\Sleek\Settings\add_setting('outdated_browser_warning', 'textarea', esc_html__('Outdated browser warning', 'sleek_admin'));
	}
});

#####################
# Add stuff to footer
add_action('wp_footer', function () {
	# Cookie consent
	if (get_theme_support('sleek/notices/cookie_consent')) {
		$cookieConsent = null;

		if ($consent = \Sleek\Settings\get_setting('cookie_consent')) {
			$cookieConsent = $consent;
		}
		else {
			$cookieUrl = get_privacy_policy_url() ? get_privacy_policy_url() : apply_filters('sleek/notices/cookie_consent_url', 'https://cookiesandyou.com/');
			$cookieConsent = __('We use cookies to bring you the best possible experience when browsing our site.', 'sleek');
			$cookieConsent .= ' <a href="' . $cookieUrl . '" target="_blank" rel="noopener">' . __('Read more', 'sleek') . '</a> | <a href="#" class="close">' . __('Accept', 'sleek') . '</a>';
			$cookieConsent = apply_filters('sleek/notices/cookie_consent', $cookieConsent, $cookieUrl);
		}

		# Add cookie consent with JS
		if ($cookieConsent) {
			?>
			<script>
				var accept = window.localStorage.getItem('sleek_cookie_consent');

				if (!accept) {
					document.documentElement.classList.add('cookie-consent--not-accepted');

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
							document.documentElement.classList.remove('cookie-consent--not-accepted');
							document.documentElement.classList.add('cookie-consent--accepted');
						});
					}
				}
				else {
					document.documentElement.classList.add('cookie-consent--accepted');
				}
			</script>
			<?php
		}
	}

	# IE warning
	if (get_theme_support('sleek/notices/outdated_browser_warning')) {
		$browserWarning = null;

		if ($warning = \Sleek\Settings\get_setting('outdated_browser_warning')) {
			$browserWarning = $warning;
		}
		else {
			$browserWarning = apply_filters('sleek/notices/outdated_browser_warning', __('<strong>Oops!</strong> Your browser is not supported. For a richer browsing experience, please consider upgrading to a better, modern browser like <a href="https://www.google.com/chrome/">Google Chrome</a>, <a href="https://www.mozilla.org/en-US/firefox/new/">Mozilla Firefox</a>, <a href="https://support.apple.com/downloads/safari">Safari</a>, <a href="https://www.opera.com/">Opera</a> or <a href="https://www.microsoft.com/en-us/windows/microsoft-edge">Microsoft Edge</a>.', 'sleek'));
		}

		if ($browserWarning) {
			?>
			<script>
				if (window.navigator.userAgent.indexOf('MSIE') > 0 || window.navigator.userAgent.indexOf('Trident/') > 0) {
					var el = document.createElement('aside');

					el.id = 'outdated-browser-warning';
					el.innerHTML = '<?php echo addslashes($browserWarning) ?>';

					document.body.appendChild(el);
				}
			</script>
			<?php
		}
	}
});
