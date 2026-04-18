( function () {
	'use strict';

	/* -------------------------------------------------------
	   Sticky header shadow on scroll
	   ------------------------------------------------------- */
	var header = document.getElementById( 'masthead' );
	if ( header ) {
		window.addEventListener( 'scroll', function () {
			if ( window.scrollY > 10 ) {
				header.classList.add( 'scrolled' );
			} else {
				header.classList.remove( 'scrolled' );
			}
		}, { passive: true } );
	}

	/* -------------------------------------------------------
	   Mobile nav hamburger toggle
	   ------------------------------------------------------- */
	var toggle   = document.getElementById( 'nav-toggle' );
	var mobileNav = document.getElementById( 'mobile-nav' );

	if ( toggle && mobileNav ) {
		toggle.addEventListener( 'click', function () {
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
			mobileNav.setAttribute( 'aria-hidden', String( expanded ) );
			mobileNav.classList.toggle( 'is-open', ! expanded );
		} );

		// Close mobile nav on outside click
		document.addEventListener( 'click', function ( e ) {
			if ( ! toggle.contains( e.target ) && ! mobileNav.contains( e.target ) ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				mobileNav.setAttribute( 'aria-hidden', 'true' );
				mobileNav.classList.remove( 'is-open' );
			}
		} );

		// Close on Escape
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && mobileNav.classList.contains( 'is-open' ) ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				mobileNav.setAttribute( 'aria-hidden', 'true' );
				mobileNav.classList.remove( 'is-open' );
				toggle.focus();
			}
		} );
	}

	/* -------------------------------------------------------
	   Smooth scroll for on-page anchor links
	   ------------------------------------------------------- */
	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest( 'a[href^="#"]' );
		if ( ! link ) return;

		var id     = link.getAttribute( 'href' ).slice( 1 );
		var target = id ? document.getElementById( id ) : null;

		if ( target ) {
			e.preventDefault();
			target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			target.setAttribute( 'tabindex', '-1' );
			target.focus( { preventScroll: true } );
		}
	} );

} )();
