/*
 * Product-page 3D showcase (Module 2, PATCH-17 — activated 2026-09 per client
 * decision: prioritize a premium look over the loading-speed score on the
 * Product/Material pages; see deploy/QA-CHECKLIST.md).
 *
 * Three.js is Product-page-only, bundled locally via npm (never a CDN), and
 * dynamically imported ONLY after the capability + connection gate passes —
 * this file stays the isolated entry the bundle analyzer checks to prove
 * three.js never leaks into a non-Product bundle (npm run build:analyze).
 *
 * Gate: WebGL context available AND not on a 2G/3G/saveData connection AND
 * not prefers-reduced-motion. On any "no", the static fallback image inside
 * the container (markup contract below) stays visible and no 3D code runs —
 * this gate is an accessibility/bandwidth safety net and is NOT part of the
 * client's accepted performance trade-off; it always applies.
 *
 * Markup contract:
 *   <div class="blackroll-3d-slot" data-blackroll-3d>
 *     <!-- any fallback content (image or text) with class blackroll-3d-slot__fallback -->
 *   </div>
 * The scene is a generic monochrome slat showcase (no product-specific asset
 * exists yet — that's the rollerblind team's asset delivery, feedback #5).
 * Swap in a real model/texture later by extending buildScene() below; the
 * mount/gate/lifecycle code does not need to change.
 */
export function canRun3D() {
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return false;
	}
	try {
		const canvas = document.createElement( 'canvas' );
		const gl = canvas.getContext( 'webgl' ) || canvas.getContext( 'experimental-webgl' );
		if ( ! gl ) {
			return false;
		}
	} catch ( e ) {
		return false;
	}
	const conn = navigator.connection;
	if ( conn && ( conn.saveData || /2g|3g/.test( conn.effectiveType || '' ) ) ) {
		return false;
	}
	return true;
}

/**
 * Build the placeholder showcase scene: a handful of vertical slats in the
 * brand monochrome palette, slowly auto-rotating. Stands in for a real
 * product model/texture until one is supplied.
 */
function buildScene( THREE ) {
	const scene = new THREE.Scene();
	scene.background = null; // Transparent — the CSS panel behind it supplies the background.

	const group = new THREE.Group();
	const slatCount = 7;
	const slatWidth = 0.55;
	const gap = 0.12;
	const totalWidth = slatCount * slatWidth + ( slatCount - 1 ) * gap;
	const geometry = new THREE.BoxGeometry( slatWidth, 3.4, 0.08 );
	const material = new THREE.MeshStandardMaterial( {
		color: 0x1a1a1a,
		roughness: 0.55,
		metalness: 0.08,
	} );

	for ( let i = 0; i < slatCount; i++ ) {
		const slat = new THREE.Mesh( geometry, material );
		slat.position.x = -totalWidth / 2 + slatWidth / 2 + i * ( slatWidth + gap );
		group.add( slat );
	}
	scene.add( group );

	scene.add( new THREE.AmbientLight( 0xffffff, 0.55 ) );
	const key = new THREE.DirectionalLight( 0xffffff, 1.1 );
	key.position.set( 2.5, 3, 4 );
	scene.add( key );
	const rim = new THREE.DirectionalLight( 0xffffff, 0.4 );
	rim.position.set( -3, -1, -2 );
	scene.add( rim );

	return { scene, group };
}

export async function mount3D( container ) {
	if ( ! container || ! canRun3D() ) {
		return; // Keep the static fallback image in place.
	}

	let THREE;
	try {
		THREE = await import( 'three' );
	} catch ( e ) {
		return; // Fetch/parse failure — fallback image stays, no thrown error.
	}

	const width = container.clientWidth || 1;
	const height = container.clientHeight || width;

	const renderer = new THREE.WebGLRenderer( { antialias: true, alpha: true } );
	renderer.setPixelRatio( Math.min( window.devicePixelRatio || 1, 2 ) );
	renderer.setSize( width, height );
	renderer.setClearColor( 0x000000, 0 );

	const camera = new THREE.PerspectiveCamera( 45, width / height, 0.1, 100 );
	camera.position.set( 0, 0, 6 );

	const { scene, group } = buildScene( THREE );

	const canvas = renderer.domElement;
	canvas.className = 'blackroll-3d-slot__canvas';
	container.appendChild( canvas );

	const fallback = container.querySelector( '.blackroll-3d-slot__fallback' );

	let frame = null;
	let running = false;

	function render() {
		group.rotation.y += 0.004;
		renderer.render( scene, camera );
		frame = window.requestAnimationFrame( render );
	}

	function start() {
		if ( running ) {
			return;
		}
		running = true;
		render();
	}

	function stop() {
		running = false;
		if ( frame ) {
			window.cancelAnimationFrame( frame );
			frame = null;
		}
	}

	// Pause the render loop while the showcase is scrolled out of view.
	if ( 'IntersectionObserver' in window ) {
		const io = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => ( entry.isIntersecting ? start() : stop() ) );
			},
			{ threshold: 0.1 }
		);
		io.observe( container );
	} else {
		start();
	}

	if ( 'ResizeObserver' in window ) {
		const ro = new ResizeObserver( ( entries ) => {
			const entry = entries[ 0 ];
			if ( ! entry ) {
				return;
			}
			const w = entry.contentRect.width || width;
			const h = entry.contentRect.height || w;
			renderer.setSize( w, h );
			camera.aspect = w / h;
			camera.updateProjectionMatrix();
		} );
		ro.observe( container );
	}

	if ( fallback ) {
		fallback.style.display = 'none';
	}
}

/**
 * Auto-mount into every `[data-blackroll-3d]` container on the page. This
 * script is only enqueued on pages that carry such a container (see
 * enqueue.php) and only when the built module bundle exists — an unbuilt dev
 * environment simply never loads this file, and the static fallback image
 * in the markup is what visitors see.
 */
document.querySelectorAll( '[data-blackroll-3d]' ).forEach( ( el ) => {
	mount3D( el );
} );
