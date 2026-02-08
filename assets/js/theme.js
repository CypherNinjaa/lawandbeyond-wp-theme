/**
 * Law and Beyond - Theme JavaScript
 *
 * Handles mobile menu, search toggle, back-to-top, and live clock.
 *
 * @package LawAndBeyond
 */

(function ($) {
	'use strict';

	/* ========================================
	   Mobile Menu Toggle
	   ======================================== */
	var $body = $('body');
	var $menuOpener = $('#menu-opener');
	var $closeMenu = $('.close-mobile-menu');
	var $overlay = $('.mobile-menu-overlay');

	function openMobileMenu() {
		$body.addClass('mobile-menu-open');
	}

	function closeMobileMenu() {
		$body.removeClass('mobile-menu-open');
	}

	$menuOpener.on('click', function (e) {
		e.preventDefault();
		openMobileMenu();
	});

	$closeMenu.on('click', function (e) {
		e.preventDefault();
		closeMobileMenu();
	});

	$overlay.on('click', function () {
		closeMobileMenu();
	});

	// Close on Escape key
	$(document).on('keyup', function (e) {
		if (e.key === 'Escape') {
			closeMobileMenu();
			closeSearchForm();
		}
	});

	/* ========================================
	   Header Search Toggle
	   ======================================== */
	var $searchIcon = $('.header-search-icon');
	var $searchForm = $('.header-search-form');

	function toggleSearchForm() {
		$searchForm.toggleClass('active');
		if ($searchForm.hasClass('active')) {
			$searchForm.find('.search-field').focus();
		}
	}

	function closeSearchForm() {
		$searchForm.removeClass('active');
	}

	$searchIcon.on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		toggleSearchForm();
	});

	// Close search when clicking outside
	$(document).on('click', function (e) {
		if (!$(e.target).closest('.header-right').length) {
			closeSearchForm();
		}
	});

	/* ========================================
	   Back to Top
	   ======================================== */
	var $backToTop = $('.backtotop');
	var scrollThreshold = 300;

	$(window).on('scroll', function () {
		if ($(this).scrollTop() > scrollThreshold) {
			$backToTop.addClass('visible');
		} else {
			$backToTop.removeClass('visible');
		}
	});

	$backToTop.on('click', function (e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: 0 }, 600);
	});

	/* ========================================
	   Live Clock / Date Display
	   ======================================== */
	var $headerDate = $('.header-date');
	var $headerClock = $('.header-clock');

	function updateDateTime() {
		var now = new Date();

		// Date string: "Sun. Feb 8th, 2026"
		var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
		var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
		              'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var day = now.getDate();
		var suffix = 'th';
		if (day === 1 || day === 21 || day === 31) suffix = 'st';
		else if (day === 2 || day === 22) suffix = 'nd';
		else if (day === 3 || day === 23) suffix = 'rd';

		var dateStr = days[now.getDay()] + '. ' +
		              months[now.getMonth()] + ' ' +
		              day + suffix + ', ' +
		              now.getFullYear();

		$headerDate.text(dateStr);
	}

	function updateClock() {
		var now = new Date();
		var h = now.getHours();
		var m = now.getMinutes();
		var s = now.getSeconds();
		var ampm = h >= 12 ? 'PM' : 'AM';
		h = h % 12;
		if (h === 0) h = 12;
		var timeStr = h + ':' +
		              (m < 10 ? '0' : '') + m + ':' +
		              (s < 10 ? '0' : '') + s + ' ' + ampm;
		$headerClock.text(timeStr);
	}

	if ($headerDate.length) {
		updateDateTime();
		setInterval(updateDateTime, 60000);
	}
	if ($headerClock.length) {
		updateClock();
		setInterval(updateClock, 1000);
	}

	/* ========================================
	   Sticky Sub-menu Handling
	   ======================================== */
	$('.main-navigation .menu-item-has-children').on('mouseenter', function () {
		var $sub = $(this).children('.sub-menu');
		var offset = $sub.offset();
		if (offset) {
			var rightEdge = offset.left + $sub.outerWidth();
			if (rightEdge > $(window).width()) {
				$sub.css({ left: 'auto', right: '0' });
			}
		}
	});

	/* ========================================
	   Realtime Live Search
	   ======================================== */
	var $searchField    = $('.header-search-form .search-field');
	var $searchResults  = $('.live-search-results');
	var searchTimer     = null;
	var lastSearchQuery = '';

	if ($searchField.length && typeof lawandbeyondAjax !== 'undefined') {
		$searchField.on('input', function () {
			var query = $(this).val().trim();

			if (query.length < 2) {
				$searchResults.html('').removeClass('active');
				lastSearchQuery = '';
				return;
			}

			if (query === lastSearchQuery) return;
			lastSearchQuery = query;

			clearTimeout(searchTimer);
			searchTimer = setTimeout(function () {
				// Show loading state
				$searchResults.html('<div class="live-search-loading"><i class="fa-solid fa-spinner fa-spin"></i> Searching&hellip;</div>').addClass('active');

				$.ajax({
					url: lawandbeyondAjax.ajaxUrl,
					type: 'POST',
					data: {
						action: 'lawandbeyond_live_search',
						nonce: lawandbeyondAjax.nonce,
						query: query
					},
					success: function (response) {
						if (response.success && response.data.html) {
							$searchResults.html(response.data.html).addClass('active');
						} else {
							$searchResults.html('').removeClass('active');
						}
					},
					error: function () {
						$searchResults.html('').removeClass('active');
					}
				});
			}, 300); // 300ms debounce
		});

		// Close live search results when clicking outside
		$(document).on('click', function (e) {
			if (!$(e.target).closest('.header-right').length) {
				$searchResults.html('').removeClass('active');
			}
		});

		// Close on Escape
		$searchField.on('keydown', function (e) {
			if (e.key === 'Escape') {
				$searchResults.html('').removeClass('active');
			}
		});
	}

	/* ========================================
	   Sidebar Widget Live Search
	   ======================================== */
	$('.widget .search-form').each(function () {
		var $form = $(this);
		// Append live search container if not present
		if (!$form.find('.live-search-results').length) {
			$form.css('position', 'relative').append('<div class="live-search-results"></div>');
		}
		var $field = $form.find('.search-field');
		var $results = $form.find('.live-search-results');
		var timer = null;
		var lastQ = '';

		if ($field.length && typeof lawandbeyondAjax !== 'undefined') {
			$field.on('input', function () {
				var q = $(this).val().trim();
				if (q.length < 2) {
					$results.html('').removeClass('active');
					lastQ = '';
					return;
				}
				if (q === lastQ) return;
				lastQ = q;
				clearTimeout(timer);
				timer = setTimeout(function () {
					$results.html('<div class="live-search-loading"><i class="fa-solid fa-spinner fa-spin"></i> Searching&hellip;</div>').addClass('active');
					$.ajax({
						url: lawandbeyondAjax.ajaxUrl,
						type: 'POST',
						data: {
							action: 'lawandbeyond_live_search',
							nonce: lawandbeyondAjax.nonce,
							query: q
						},
						success: function (response) {
							if (response.success && response.data.html) {
								$results.html(response.data.html).addClass('active');
							} else {
								$results.html('').removeClass('active');
							}
						},
						error: function () {
							$results.html('').removeClass('active');
						}
					});
				}, 300);
			});

			// Close on click outside
			$(document).on('click', function (e) {
				if (!$(e.target).closest($form).length) {
					$results.html('').removeClass('active');
				}
			});

			$field.on('keydown', function (e) {
				if (e.key === 'Escape') {
					$results.html('').removeClass('active');
				}
			});
		}
	});

	/* ========================================
	   Mobile Bottom Navigation
	   ======================================== */
	// Mobile bottom menu button opens the side drawer
	$('#mobile-bottom-menu-btn').on('click', function (e) {
		e.preventDefault();
		openMobileMenu();
	});

	// Mobile search overlay
	var $mobileSearchOverlay = $('.mobile-search-overlay');
	var $mobileSearchField   = $mobileSearchOverlay.find('.search-field');
	var $mobileSearchResults = $mobileSearchOverlay.find('.mobile-live-search-results');

	$('.mobile-bottom-search-btn').on('click', function (e) {
		e.preventDefault();
		$mobileSearchOverlay.show();
		setTimeout(function () { $mobileSearchField.focus(); }, 100);
	});

	$('.mobile-search-overlay__close').on('click', function () {
		$mobileSearchOverlay.hide();
		$mobileSearchResults.html('').removeClass('active');
		$mobileSearchField.val('');
	});

	// Close overlay on backdrop click
	$mobileSearchOverlay.on('click', function (e) {
		if ($(e.target).hasClass('mobile-search-overlay')) {
			$mobileSearchOverlay.hide();
			$mobileSearchResults.html('').removeClass('active');
			$mobileSearchField.val('');
		}
	});

	// Live search for mobile overlay
	if ($mobileSearchField.length && typeof lawandbeyondAjax !== 'undefined') {
		var mobileSearchTimer = null;
		var mobileLastQuery   = '';

		$mobileSearchField.on('input', function () {
			var query = $(this).val().trim();

			if (query.length < 2) {
				$mobileSearchResults.html('').removeClass('active');
				mobileLastQuery = '';
				return;
			}

			if (query === mobileLastQuery) return;
			mobileLastQuery = query;

			clearTimeout(mobileSearchTimer);
			mobileSearchTimer = setTimeout(function () {
				$mobileSearchResults.html('<div class="live-search-loading"><i class="fa-solid fa-spinner fa-spin"></i> Searching&hellip;</div>').addClass('active');

				$.ajax({
					url: lawandbeyondAjax.ajaxUrl,
					type: 'POST',
					data: {
						action: 'lawandbeyond_live_search',
						nonce: lawandbeyondAjax.nonce,
						query: query
					},
					success: function (response) {
						if (response.success && response.data.html) {
							$mobileSearchResults.html(response.data.html).addClass('active');
						} else {
							$mobileSearchResults.html('').removeClass('active');
						}
					}
				});
			}, 300);
		});
	}

	/* ========================================
	   Push Notification Subscribe Buttons
	   (Theme-level handler — works with LegalPress plugin)
	   ======================================== */
	(function () {
		// Check browser support
		if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
			return;
		}

		// Check that the plugin is loaded (provides legalpressPush global)
		if (typeof legalpressPush === 'undefined' || !legalpressPush.vapidPublicKey) {
			return;
		}

		var $allBtns = $('.lab-push-subscribe-btn');
		if (!$allBtns.length) return;

		// Show subscribe UI (hidden by default via inline style)
		$allBtns.each(function () { $(this).show(); });
		$('.subscribe-widget').show();

		// Convert VAPID key from base64url to Uint8Array
		function urlBase64ToUint8Array(base64String) {
			var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
			var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
			var rawData = window.atob(base64);
			var arr = new Uint8Array(rawData.length);
			for (var i = 0; i < rawData.length; i++) {
				arr[i] = rawData.charCodeAt(i);
			}
			return arr;
		}

		// Update all buttons to given state
		function updateAllBtns(state) {
			$allBtns.each(function () {
				var $btn = $(this);
				$btn.attr('data-state', state);
				$btn.prop('disabled', false);
				if (state === 'subscribed') {
					$btn.html('<i class="fa-solid fa-bell"></i> <span>Subscribed</span>');
					$btn.prop('disabled', true);
				} else if (state === 'subscribing') {
					$btn.html('<i class="fa-solid fa-spinner fa-spin"></i> <span>Subscribing…</span>');
					$btn.prop('disabled', true);
				} else {
					$btn.html('<i class="fa-solid fa-bell"></i> <span>Subscribe Now</span>');
				}
			});
		}

		// Save subscription to server (same AJAX the plugin uses)
		function saveToServer(subscription) {
			return $.ajax({
				url: legalpressPush.ajaxUrl,
				type: 'POST',
				data: {
					action: 'legalpress_save_subscription',
					nonce: legalpressPush.nonce,
					subscription: JSON.stringify(subscription.toJSON())
				}
			});
		}

		// Initialize — register SW, check subscription, wire buttons
		navigator.serviceWorker.register(legalpressPush.serviceWorkerUrl, { scope: '/' })
			.then(function (registration) {
				return registration.pushManager.getSubscription().then(function (sub) {
					if (sub) {
						updateAllBtns('subscribed');
					}
					return registration;
				});
			})
			.then(function (registration) {
				$allBtns.on('click', function () {
					var $btn = $(this);
					if ($btn.attr('data-state') === 'subscribed') return;

					updateAllBtns('subscribing');

					Notification.requestPermission().then(function (perm) {
						if (perm !== 'granted') {
							updateAllBtns('idle');
							alert('Please allow notifications in your browser to subscribe.');
							return;
						}

						registration.pushManager.subscribe({
							userVisibleOnly: true,
							applicationServerKey: urlBase64ToUint8Array(legalpressPush.vapidPublicKey)
						}).then(function (subscription) {
							return saveToServer(subscription);
						}).then(function () {
							updateAllBtns('subscribed');
						}).catch(function (err) {
							console.error('[LAB Push] Subscribe failed:', err);
							updateAllBtns('idle');
							alert('Subscription failed. Please try again.');
						});
					});
				});
			})
			.catch(function (err) {
				console.error('[LAB Push] SW registration failed:', err);
			});
	})();

})(jQuery);
