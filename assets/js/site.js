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
		var timer = window.setInterval(function () { show(current + 1); }, interval);

		function stop() {
			window.clearInterval(timer);
			timer = null;
		}

		root.addEventListener('mouseenter', stop);
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
		};
		window.addEventListener('scroll', updateProgress, { passive: true });
		window.addEventListener('resize', updateProgress);
		updateProgress();

		var elements = document.querySelectorAll('.panel, .widget, .band, .footer-col');
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
			element.style.transitionDelay = Math.min(index % 4, 3) * 55 + 'ms';
			observer.observe(element);
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initPageLoader();
		initNavToggle();
		Array.prototype.forEach.call(document.querySelectorAll('[data-slides]'), initSlides);
		initPageTransitions();
		initScrollEffects();
	});
}());
