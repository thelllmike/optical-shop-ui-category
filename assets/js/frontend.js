/**
 * Optical Shop UI – Frontend JS
 *
 * Handles:
 *  1. Desktop: hover-to-play / leave-to-pause for video cards.
 *  2. Mobile:  IntersectionObserver auto-play when card ≥ 60 % visible.
 *  3. Global:  "pause all others" when a new video starts playing.
 */
( function () {
	'use strict';

	/* ── Helpers ─────────────────────────────────────────── */

	var MOBILE_BREAKPOINT = 768;

	function isMobile() {
		return window.innerWidth < MOBILE_BREAKPOINT;
	}

	/**
	 * Pause every trending video except the one passed.
	 *
	 * @param {HTMLVideoElement|null} except Video element to skip.
	 */
	function pauseAllExcept( except ) {
		var videos = document.querySelectorAll( '.osui-trending__video' );
		videos.forEach( function ( v ) {
			if ( v !== except && ! v.paused ) {
				v.pause();
				// Reset to first frame.
				v.currentTime = 0;
			}
		} );
	}

	/**
	 * Safely play a video, handling the promise rejection that
	 * browsers throw when autoplay is blocked.
	 *
	 * @param {HTMLVideoElement} video
	 */
	function safePlay( video ) {
		pauseAllExcept( video );
		var playPromise = video.play();
		if ( playPromise !== undefined ) {
			playPromise.catch( function () {
				// Autoplay blocked — silently ignore.
			} );
		}
	}

	/* ── Desktop: hover play / leave pause ───────────────── */

	function initDesktopHover() {
		var cards = document.querySelectorAll( '.osui-trending__card--video' );

		cards.forEach( function ( card ) {
			var video = card.querySelector( 'video' );
			if ( ! video ) return;

			card.addEventListener( 'mouseenter', function () {
				if ( isMobile() ) return;
				safePlay( video );
			} );

			card.addEventListener( 'mouseleave', function () {
				if ( isMobile() ) return;
				video.pause();
				video.currentTime = 0;
			} );
		} );
	}

	/* ── Mobile: IntersectionObserver ────────────────────── */

	function initMobileObserver() {
		if ( typeof IntersectionObserver === 'undefined' ) {
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				if ( ! isMobile() ) return;

				entries.forEach( function ( entry ) {
					var video = entry.target.querySelector( 'video' );
					if ( ! video ) return;

					if ( entry.isIntersecting && entry.intersectionRatio >= 0.6 ) {
						safePlay( video );
					} else {
						video.pause();
						video.currentTime = 0;
					}
				} );
			},
			{
				threshold: [ 0, 0.6, 1 ],
			}
		);

		var cards = document.querySelectorAll( '.osui-trending__card--video' );
		cards.forEach( function ( card ) {
			observer.observe( card );
		} );
	}

	/* ── Init ────────────────────────────────────────────── */

	function init() {
		initDesktopHover();
		initMobileObserver();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
