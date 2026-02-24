/**
 * Optical Shop UI – Frontend JS
 *
 * Handles:
 *  1. Desktop: hover-to-play / leave-to-pause for video cards.
 *  2. Mobile:  IntersectionObserver auto-play when card ≥ 60 % visible.
 *  3. Auto-rotate: videos play one-by-one. After ending → show poster → next.
 *  4. Slider: prev/next arrows when more than 4 cards (desktop).
 *  5. Global: only one video plays at a time.
 */
( function () {
	'use strict';

	var MOBILE_BREAKPOINT = 768;

	function isMobile() {
		return window.innerWidth < MOBILE_BREAKPOINT;
	}

	/* ══════════════════════════════════════════════════════════
	   Shared video helpers
	   ══════════════════════════════════════════════════════════ */

	function resetVideo( video ) {
		if ( ! video ) return;
		video.pause();
		video.currentTime = 0;
		video.load(); // forces poster to re-appear
	}

	function pauseAllExcept( except ) {
		document.querySelectorAll( '.osui-trending__video' ).forEach( function ( v ) {
			if ( v !== except && ! v.paused ) {
				resetVideo( v );
			}
		} );
	}

	function safePlay( video ) {
		pauseAllExcept( video );
		var p = video.play();
		if ( p !== undefined ) {
			p.catch( function () { /* autoplay blocked */ } );
		}
	}

	/* ══════════════════════════════════════════════════════════
	   Generic slider controller (works for shapes + trending)
	   Uses .osui-slider, .osui-slider__track, .osui-slider__arrow
	   ══════════════════════════════════════════════════════════ */

	function initSliders() {
		document.querySelectorAll( '.osui-slider' ).forEach( function ( slider ) {
			var track    = slider.querySelector( '.osui-slider__track' );
			var prevBtn  = slider.querySelector( '.osui-slider__arrow--prev' );
			var nextBtn  = slider.querySelector( '.osui-slider__arrow--next' );

			if ( ! track || ! prevBtn || ! nextBtn ) return;

			// Get the first child item of the track (tile or card).
			var firstItem = track.children[0];
			if ( ! firstItem ) return;

			function checkOverflow() {
				var hasOverflow = track.scrollWidth > track.clientWidth + 2;
				if ( hasOverflow ) {
					slider.classList.add( 'osui-slider--has-overflow' );
				} else {
					slider.classList.remove( 'osui-slider--has-overflow' );
				}
				updateArrows();
			}

			function updateArrows() {
				var scrollLeft = Math.round( track.scrollLeft );
				var maxScroll  = track.scrollWidth - track.clientWidth;
				prevBtn.disabled = scrollLeft <= 2;
				nextBtn.disabled = scrollLeft >= maxScroll - 2;
			}

			function getItemWidth() {
				var item = track.children[0];
				if ( ! item ) return 200;
				var style = window.getComputedStyle( track );
				var gap   = parseInt( style.gap || style.columnGap, 10 ) || 16;
				return item.offsetWidth + gap;
			}

			prevBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				track.scrollBy( { left: -getItemWidth(), behavior: 'smooth' } );
			} );

			nextBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				track.scrollBy( { left: getItemWidth(), behavior: 'smooth' } );
			} );

			track.addEventListener( 'scroll', updateArrows );
			window.addEventListener( 'resize', checkOverflow );
			checkOverflow();
		} );
	}

	/* ══════════════════════════════════════════════════════════
	   Per-row auto-rotate controller
	   ══════════════════════════════════════════════════════════ */

	function RowController( rowEl ) {
		var self = this;

		self.row    = rowEl;
		self.cards  = Array.prototype.slice.call(
			rowEl.querySelectorAll( '.osui-trending__card--video' )
		);
		self.videos = self.cards.map( function ( c ) {
			return c.querySelector( 'video' );
		} ).filter( Boolean );

		self.currentIndex = 0;
		self.hoverPaused  = false;
		self.autoTimer    = null;

		if ( self.videos.length === 0 ) return;

		// When a video ends → reset to poster → advance to next.
		self.videos.forEach( function ( video, idx ) {
			video.addEventListener( 'ended', function () {
				resetVideo( video );
				if ( self.hoverPaused ) return;
				self.currentIndex = ( idx + 1 ) % self.videos.length;
				self.scheduleNext( 1500 );
			} );
		} );

		// Desktop hover: play on enter, reset + resume rotation on leave.
		self.cards.forEach( function ( card, idx ) {
			var video = self.videos[ idx ];
			if ( ! video ) return;

			card.addEventListener( 'mouseenter', function () {
				if ( isMobile() ) return;
				self.hoverPaused = true;
				self.clearTimer();
				safePlay( video );
			} );

			card.addEventListener( 'mouseleave', function () {
				if ( isMobile() ) return;
				self.hoverPaused = false;
				resetVideo( video );
				self.currentIndex = ( idx + 1 ) % self.videos.length;
				self.scheduleNext( 2000 );
			} );
		} );

		// Start first auto-play after initial delay.
		self.scheduleNext( 2500 );
	}

	RowController.prototype.scheduleNext = function ( delay ) {
		var self = this;
		self.clearTimer();
		self.autoTimer = setTimeout( function () {
			if ( self.hoverPaused ) return;
			if ( isMobile() ) return;
			self.playIndex( self.currentIndex );
		}, delay );
	};

	RowController.prototype.playIndex = function ( idx ) {
		var video = this.videos[ idx ];
		if ( ! video ) return;

		// Auto-scroll so the playing card is visible.
		var card = this.cards[ idx ];
		if ( card && this.row ) {
			var rowRect  = this.row.getBoundingClientRect();
			var cardRect = card.getBoundingClientRect();
			if ( cardRect.left < rowRect.left || cardRect.right > rowRect.right ) {
				card.scrollIntoView( { behavior: 'smooth', inline: 'start', block: 'nearest' } );
			}
		}

		safePlay( video );
	};

	RowController.prototype.clearTimer = function () {
		if ( this.autoTimer ) {
			clearTimeout( this.autoTimer );
			this.autoTimer = null;
		}
	};

	/* ══════════════════════════════════════════════════════════
	   Mobile: IntersectionObserver
	   ══════════════════════════════════════════════════════════ */

	function initMobileObserver() {
		if ( typeof IntersectionObserver === 'undefined' ) return;

		var observer = new IntersectionObserver(
			function ( entries ) {
				if ( ! isMobile() ) return;
				entries.forEach( function ( entry ) {
					var video = entry.target.querySelector( 'video' );
					if ( ! video ) return;
					if ( entry.isIntersecting && entry.intersectionRatio >= 0.6 ) {
						safePlay( video );
					} else {
						resetVideo( video );
					}
				} );
			},
			{ threshold: [ 0, 0.6, 1 ] }
		);

		document.querySelectorAll( '.osui-trending__card--video' ).forEach( function ( card ) {
			observer.observe( card );
		} );
	}

	/* ══════════════════════════════════════════════════════════
	   Init
	   ══════════════════════════════════════════════════════════ */

	function init() {
		// Slider arrows.
		initSliders();

		// Auto-rotate controller per row.
		document.querySelectorAll( '.osui-trending__row' ).forEach( function ( rowEl ) {
			new RowController( rowEl );
		} );

		// Mobile observer.
		initMobileObserver();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
