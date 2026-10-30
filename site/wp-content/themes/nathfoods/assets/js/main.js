( function () {
	'use strict';

	var M = window.Motion || null;
	var calm = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var fast = { duration: 0.3, ease: [ 0.2, 0.7, 0.2, 1 ] };

	function revealAll( list ) {
		list.forEach( function ( el ) {
			el.classList.add( 'is-in' );
			el.style.opacity = '';
			el.style.transform = '';
		} );
	}

	// Scroll reveals, staggered per viewport entry. A CSS transition, so content can never stick hidden.
	window.NF_READY = true;
	var revealEls = Array.prototype.slice.call( document.querySelectorAll( '[data-reveal]' ) );
	if ( calm || ! ( 'IntersectionObserver' in window ) ) {
		revealAll( revealEls );
	} else {
		var observer = new IntersectionObserver( function ( entries ) {
			var i = 0;
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					observer.unobserve( entry.target );
					entry.target.style.setProperty( '--nf-delay', ( i++ * 40 ) + 'ms' );
					entry.target.classList.add( 'is-in' );
				}
			} );
		}, { rootMargin: '0px 0px -40px 0px' } );
		revealEls.forEach( function ( el ) { observer.observe( el ); } );
	}

	if ( M && ! calm ) {
		// Hero entrance.
		var hero = document.querySelector( '[data-hero]' );
		if ( hero ) {
			M.animate( Array.prototype.slice.call( hero.children ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration: 0.4, delay: M.stagger( 0.07 ), ease: fast.ease } );
		}
		var art = document.querySelectorAll( '[data-hero-art] img' );
		if ( art.length ) {
			M.animate( art, { opacity: [ 0, 1 ], scale: [ 0.85, 1 ] }, { duration: 0.45, delay: M.stagger( 0.06, { startDelay: 0.15 } ), ease: fast.ease } );
		}
	}

	// Sticky header state.
	var header = document.getElementById( 'nf-header' );
	if ( header ) {
		var onScroll = function () { header.classList.toggle( 'is-stuck', window.scrollY > 8 ); };
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	// Mobile drawer.
	var drawer = document.getElementById( 'nf-drawer' );
	var toggle = document.querySelector( '.nf-menu-toggle' );
	if ( drawer && toggle ) {
		var panel = drawer.querySelector( '.nf-drawer__panel' );
		var closeBtn = drawer.querySelector( '.nf-drawer__close' );
		var setOpen = function ( open ) {
			drawer.hidden = ! open;
			toggle.setAttribute( 'aria-expanded', String( open ) );
			document.body.style.overflow = open ? 'hidden' : '';
			if ( open ) {
				closeBtn.focus();
				if ( M && ! calm ) {
					M.animate( panel, { x: [ '-100%', 0 ] }, fast );
				}
			} else {
				toggle.focus();
			}
		};
		toggle.addEventListener( 'click', function () { setOpen( true ); } );
		closeBtn.addEventListener( 'click', function () { setOpen( false ); } );
		drawer.addEventListener( 'click', function ( e ) { if ( e.target === drawer ) { setOpen( false ); } } );
		document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' && ! drawer.hidden ) { setOpen( false ); } } );
	}

	// Category tabs on the home page.
	document.querySelectorAll( '[data-filter]' ).forEach( function ( group ) {
		var target = document.querySelector( group.getAttribute( 'data-filter' ) );
		if ( ! target ) { return; }
		group.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button' );
			if ( ! btn ) { return; }
			group.querySelectorAll( 'button' ).forEach( function ( b ) {
				b.classList.toggle( 'is-active', b === btn );
				b.setAttribute( 'aria-pressed', String( b === btn ) );
			} );
			var cat = btn.getAttribute( 'data-cat' );
			var shown = [];
			target.querySelectorAll( '.nf-card' ).forEach( function ( card ) {
				var match = ! cat || ( ' ' + card.getAttribute( 'data-cats' ) + ' ' ).indexOf( ' ' + cat + ' ' ) !== -1;
				card.hidden = ! match;
				if ( match ) { shown.push( card ); }
			} );
			revealAll( shown );
			if ( M && ! calm && shown.length ) {
				M.animate( shown, { opacity: [ 0, 1 ], y: [ 8, 0 ] }, { duration: 0.22, delay: M.stagger( 0.03 ), ease: fast.ease } );
			}
		} );
	} );

	// Carousel arrows.
	document.querySelectorAll( '[data-scroll-target]' ).forEach( function ( arrows ) {
		var track = document.querySelector( arrows.getAttribute( 'data-scroll-target' ) );
		if ( ! track ) { return; }
		arrows.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button' );
			if ( btn ) {
				track.scrollBy( { left: Number( btn.getAttribute( 'data-dir' ) ) * track.clientWidth * 0.75, behavior: calm ? 'auto' : 'smooth' } );
			}
		} );
	} );

	// Cart count pop after AJAX add to cart.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'added_to_cart', function () {
			var count = document.querySelector( '.nf-cart-count' );
			if ( count && M && ! calm ) {
				M.animate( count, { scale: [ 1, 1.35, 1 ] }, { duration: 0.3 } );
			}
		} );
	}
}() );
