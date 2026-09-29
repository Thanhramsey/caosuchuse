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

	document.addEventListener('DOMContentLoaded', function () {
		initNavToggle();
		Array.prototype.forEach.call(document.querySelectorAll('[data-slides]'), initSlides);
	});
}());
