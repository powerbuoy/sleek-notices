<?php
namespace Sleek\Notices;

####################
# Add settings field
add_action('admin_init', function () {
	\Sleek\Settings\add_setting('cookie_consent', 'textarea', esc_html__('Cookie consent text', 'sleek'));
	\Sleek\Settings\add_setting('site_notice', 'textarea', esc_html__('Site notice', 'sleek'));
});

#####################
# Add stuff to footer
add_action('wp_footer', function () {
	$cookieConsent = null;

	# Cookie consent text set in admin
	if ($consent = \Sleek\Settings\get_setting('cookie_consent')) {
		$cookieConsent = $consent;
	}
	# Fallback text
	elseif (get_theme_support('sleek-cookie-consent')) {
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

	# Site notice
	if ($notice = \Sleek\Settings\get_setting('site_notice')) {
		echo '<aside id="site-notice">' . $notice . '</aside>';
	}
});
