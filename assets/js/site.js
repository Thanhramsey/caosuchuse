(function () {
	'use strict';

	function initNavToggle() {
		var toggle = document.querySelector('[data-nav-toggle]');
		var menu = document.getElementById('main-menu');
		if (!toggle || !menu) { return; }
		toggle.addEventListener('click', function () {
			var open = menu.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	function initSlides(root) {
		var slides = Array.prototype.filter.call(root.children, function (node) {
			return node.classList && node.classList.contains('slide');
		});
		if (slides.length < 2) { return; }

		var current = 0;
		var dots = document.createElement('div');
		dots.className = 'dots';

		function show(index) {
			slides[current].classList.remove('is-active');
			dots.children[current].classList.remove('is-active');
			dots.children[current].setAttribute('aria-selected', 'false');
			current = (index + slides.length) % slides.length;
			slides[current].classList.add('is-active');
			dots.children[current].classList.add('is-active');
			dots.children[current].setAttribute('aria-selected', 'true');
		}

		slides.forEach(function (slide, index) {
			var dot = document.createElement('button');
			dot.type = 'button';
			dot.setAttribute('role', 'tab');
			dot.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
			dot.setAttribute('aria-label', 'Xem mục ' + (index + 1));
			if (index === 0) { dot.className = 'is-active'; }
			dot.addEventListener('click', function () {
				show(index);
				stop();
			});
			dots.appendChild(dot);
		});
		dots.setAttribute('role', 'tablist');
		root.parentNode.insertBefore(dots, root.nextSibling);

		var interval = parseInt(root.getAttribute('data-interval'), 10) || 5000;
		var timer = null;
		function start() {
			if (timer || document.hidden) { return; }
			timer = window.setInterval(function () { show(current + 1); }, interval);
		}

		function stop() {
			if (!timer) { return; }
			window.clearInterval(timer);
			timer = null;
		}

		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', start);
		document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });
		start();
	}

	function initPageLoader() {
		var reveal = function () { document.body.classList.add('site-ready'); };
		if (document.readyState === 'complete') { reveal(); }
		else { window.addEventListener('load', reveal); }
		window.setTimeout(reveal, 1800);
		window.addEventListener('pageshow', function () {
			document.body.classList.remove('page-is-leaving');
			reveal();
		});
	}

	function initPageTransitions() {
		document.addEventListener('click', function (event) {
			var link = event.target.closest ? event.target.closest('a[href]') : null;
			if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
			if (link.target === '_blank' || link.hasAttribute('download')) return;
			var href = link.getAttribute('href');
			if (!href || href.charAt(0) === '#' || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;
			var target;
			try { target = new URL(link.href, window.location.href); } catch (error) { return; }
			if (target.origin !== window.location.origin || target.href === window.location.href) return;
			event.preventDefault();
			document.body.classList.add('page-is-leaving');
			window.setTimeout(function () { window.location.href = target.href; }, 170);
		});
	}

	function initScrollEffects() {
		var progress = document.querySelector('.site-scroll-progress span');
		var updateProgress = function () {
			if (!progress) return;
			var maximum = document.documentElement.scrollHeight - window.innerHeight;
			var value = maximum > 0 ? Math.min(100, Math.max(0, window.pageYOffset / maximum * 100)) : 0;
			progress.style.width = value + '%';
			document.body.classList.toggle('has-scrolled', window.pageYOffset > 24);
		};
		window.addEventListener('scroll', updateProgress, { passive: true });
		window.addEventListener('resize', updateProgress);
		updateProgress();

		var elements = document.querySelectorAll('.panel, .widget, .band, .footer-col, .news-card, .product-card, .home-stat, .home-highlight, .home-orientation > div');
		if (!('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(elements, function (element) { element.classList.add('is-revealed'); });
			return;
		}
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				entry.target.classList.add('is-revealed');
				observer.unobserve(entry.target);
			});
		}, { rootMargin: '0px 0px -40px', threshold: .08 });
		Array.prototype.forEach.call(elements, function (element, index) {
			element.classList.add('reveal-ready');
			if (element.matches('.news-card, .product-card, .home-stat, .home-highlight, .home-orientation > div')) {
				element.classList.add('reveal-child');
			}
			element.style.transitionDelay = Math.min(index % 4, 3) * 65 + 'ms';
			observer.observe(element);
		});
	}

	function initCounters() {
		var counters = document.querySelectorAll('[data-count]');
		if (!counters.length) { return; }
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		function format(value) { return Math.round(value).toLocaleString('vi-VN'); }
		function run(element) {
			if (element.getAttribute('data-counted') === 'true') { return; }
			element.setAttribute('data-counted', 'true');
			var target = parseInt(element.getAttribute('data-count'), 10) || 0;
			var suffix = element.getAttribute('data-suffix') || '';
			if (reduceMotion) { element.textContent = format(target) + suffix; return; }
			var started = null;
			function frame(timestamp) {
				if (!started) { started = timestamp; }
				var progress = Math.min(1, (timestamp - started) / 1050);
				var eased = 1 - Math.pow(1 - progress, 3);
				element.textContent = format(target * eased) + suffix;
				if (progress < 1) { window.requestAnimationFrame(frame); }
			}
			window.requestAnimationFrame(frame);
		}
		if (!('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(counters, run);
			return;
		}
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				run(entry.target);
				observer.unobserve(entry.target);
			});
		}, { threshold: .35 });
		Array.prototype.forEach.call(counters, function (counter) { observer.observe(counter); });
	}

	function initBackToTop() {
		var button = document.createElement('button');
		button.type = 'button';
		button.className = 'back-to-top';
		button.setAttribute('aria-label', 'Về đầu trang');
		button.innerHTML = '&uarr;';
		document.body.appendChild(button);
		function update() { button.classList.toggle('is-visible', window.pageYOffset > 500); }
		window.addEventListener('scroll', update, { passive: true });
		button.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
		update();
	}

	document.addEventListener('DOMContentLoaded', function () {
		initPageLoader();
		initNavToggle();
		Array.prototype.forEach.call(document.querySelectorAll('[data-slides]'), initSlides);
		initPageTransitions();
		initScrollEffects();
		initCounters();
		initBackToTop();
	});
}());
