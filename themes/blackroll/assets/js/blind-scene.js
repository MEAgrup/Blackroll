/*
 * Blackroll blind scene (Fase 2) — one WebGL scene shared by the homepage hero
 * ("The Blind Reveal") and the Product-page 3D slot.
 *
 * A roller blind hangs in front of a lit window. Its fabric uses the real
 * swatch photos from the client's Sept 2026 shoot (assets/images/collection/).
 * `progress` (0 = fully down, 1 = rolled up) drives the roll; in zebra mode
 * it slides the two striped layers instead of rolling.
 *
 * Only ever reached through a dynamic import() from home-hero-3d.js or
 * product-3d.js, after canRun3D() (three-gate.js) has passed. Named imports
 * keep three.js tree-shaken.
 */
import {
	AmbientLight,
	BoxGeometry,
	CanvasTexture,
	Color,
	CylinderGeometry,
	DirectionalLight,
	DoubleSide,
	Group,
	MathUtils,
	Mesh,
	MeshBasicMaterial,
	MeshStandardMaterial,
	MirroredRepeatWrapping,
	PerspectiveCamera,
	PlaneGeometry,
	Scene,
	SphereGeometry,
	SRGBColorSpace,
	TextureLoader,
	WebGLRenderer,
} from 'three';

const W = 3.2; // Window / fabric width (scene units).
const H = 4.0; // Window height.
const TOP = H / 2 + 0.08; // Roller tube centre.

function canvasTexture( w, h, draw ) {
	const c = document.createElement( 'canvas' );
	c.width = w;
	c.height = h;
	draw( c.getContext( '2d' ), w, h );
	const t = new CanvasTexture( c );
	t.colorSpace = SRGBColorSpace;
	return t;
}

/** Warm daylight behind the window glass, with soft mullion shadows. */
function daylightTexture() {
	return canvasTexture( 256, 320, ( g, w, h ) => {
		const grad = g.createLinearGradient( 0, 0, 0, h );
		grad.addColorStop( 0, '#fffaf0' );
		grad.addColorStop( 0.55, '#f6e7cc' );
		grad.addColorStop( 1, '#e9d3ad' );
		g.fillStyle = grad;
		g.fillRect( 0, 0, w, h );
		g.fillStyle = 'rgba(40,32,20,0.18)';
		g.fillRect( w / 2 - 3, 0, 6, h );
		g.fillRect( 0, h * 0.42, w, 6 );
	} );
}

/** Zebra alpha map: alternating solid / sheer horizontal bands. */
function zebraAlpha() {
	const t = canvasTexture( 4, 64, ( g, w, h ) => {
		g.fillStyle = '#ffffff';
		g.fillRect( 0, 0, w, h / 2 );
		g.fillStyle = '#3a3a3a'; // ~23% opacity: the sheer band.
		g.fillRect( 0, h / 2, w, h / 2 );
	} );
	t.wrapS = t.wrapT = MirroredRepeatWrapping;
	t.repeat.set( 1, 7 );
	return t;
}

/**
 * Mount the scene into `container` (its size decides the canvas size).
 *
 * @param {HTMLElement} container
 * @param {{ texture: string, mode?: 'roller'|'zebra', chain?: boolean, intro?: boolean }} opts
 * @return {Promise<{ setProgress(p:number):void, setTexture(src:string):Promise<void>, setMode(m:string):void, destroy():void }>}
 */
export async function mountBlindScene( container, opts ) {
	const mobile = window.matchMedia( '(max-width: 781px)' ).matches;
	const renderer = new WebGLRenderer( { antialias: ! mobile, alpha: true, powerPreference: 'low-power' } );
	renderer.setPixelRatio( Math.min( window.devicePixelRatio || 1, mobile ? 1.5 : 2 ) );
	renderer.outputColorSpace = SRGBColorSpace;
	renderer.setClearColor( 0x000000, 0 );

	const scene = new Scene();
	const camera = new PerspectiveCamera( 32, 1, 0.1, 100 );
	camera.position.set( 0, 0.1, 10.5 );

	scene.add( new AmbientLight( 0xffffff, 0.75 ) );
	const key = new DirectionalLight( 0xffffff, 1.5 );
	key.position.set( -3, 4, 6 );
	scene.add( key );
	const fill = new DirectionalLight( 0xffffff, 0.45 );
	fill.position.set( 4, -1, 5 );
	scene.add( fill );

	const rig = new Group();
	scene.add( rig );

	// Window: glowing daylight panel + charcoal frame.
	const glass = new Mesh( new PlaneGeometry( W, H ), new MeshBasicMaterial( { map: daylightTexture() } ) );
	glass.position.z = -0.12;
	rig.add( glass );
	const frameMat = new MeshStandardMaterial( { color: 0x1c1c1c, roughness: 0.6, metalness: 0.2 } );
	const t = 0.09;
	[
		[ W + t * 2, t, 0, H / 2 + t / 2 ],
		[ W + t * 2, t, 0, -H / 2 - t / 2 ],
		[ t, H, -W / 2 - t / 2, 0 ],
		[ t, H, W / 2 + t / 2, 0 ],
	].forEach( ( [ w, h, x, y ] ) => {
		const m = new Mesh( new BoxGeometry( w, h, 0.14 ), frameMat );
		m.position.set( x, y, -0.08 );
		rig.add( m );
	} );

	// Hardware.
	const black = new MeshStandardMaterial( { color: 0x111111, roughness: 0.45, metalness: 0.35 } );
	const tubeMat = new MeshStandardMaterial( { color: 0xe9e9e9, roughness: 0.5, metalness: 0.1 } );
	const tube = new Mesh( new CylinderGeometry( 0.11, 0.11, W + 0.06, 32 ), tubeMat );
	tube.rotation.z = Math.PI / 2;
	tube.position.set( 0, TOP, 0.2 );
	rig.add( tube );
	const headbox = new Mesh( new BoxGeometry( W + 0.22, 0.34, 0.3 ), black ); // Zebra cassette.
	headbox.position.set( 0, TOP + 0.02, 0.2 );
	rig.add( headbox );
	[ -1, 1 ].forEach( ( s ) => {
		const b = new Mesh( new BoxGeometry( 0.08, 0.3, 0.32 ), black );
		b.position.set( s * ( W / 2 + 0.07 ), TOP, 0.16 );
		rig.add( b );
	} );
	const bottomBar = new Mesh( new BoxGeometry( W + 0.04, 0.08, 0.07 ), black );
	rig.add( bottomBar );

	// Chain (manual only): a thin cord + bead weight on the right.
	const chain = new Group();
	const cord = new Mesh( new CylinderGeometry( 0.012, 0.012, 2.4, 6 ), black );
	cord.position.y = -1.2;
	chain.add( cord );
	const bead = new Mesh( new SphereGeometry( 0.06, 16, 12 ), black );
	bead.position.y = -2.45;
	chain.add( bead );
	chain.position.set( W / 2 + 0.16, TOP, 0.24 );
	rig.add( chain );

	// Fabric. Two layers so zebra mode can slide sheer/solid bands over each other.
	const fabricGeo = new PlaneGeometry( W, 1 );
	fabricGeo.translate( 0, -0.5, 0 ); // Pivot at the top edge: scale.y = drop length.
	const front = new MeshStandardMaterial( { roughness: 0.92, metalness: 0, side: DoubleSide, transparent: true } );
	const back = front.clone();
	const fabric = new Mesh( fabricGeo, front );
	const fabricBack = new Mesh( fabricGeo, back );
	fabric.position.set( 0, TOP - 0.11, 0.22 );
	fabricBack.position.set( 0, TOP - 0.11, 0.19 );
	rig.add( fabricBack );
	rig.add( fabric );
	const alpha = zebraAlpha();
	const alphaBack = alpha.clone();
	alphaBack.needsUpdate = true;

	const loader = new TextureLoader();
	const cache = new Map();
	function loadTexture( src ) {
		if ( ! cache.has( src ) ) {
			cache.set(
				src,
				loader.loadAsync( src ).then( ( tex ) => {
					tex.colorSpace = SRGBColorSpace;
					tex.wrapS = tex.wrapT = MirroredRepeatWrapping;
					tex.anisotropy = Math.min( 4, renderer.capabilities.getMaxAnisotropy() );
					return tex;
				} )
			);
		}
		return cache.get( src );
	}

	const state = {
		mode: opts.mode === 'zebra' ? 'zebra' : 'roller',
		progress: opts.intro ? 0.9 : 0, // Intro: starts rolled up, drops down.
		target: 0,
		fade: 1,
		pointerX: 0,
		pointerY: 0,
		rotX: 0,
		rotY: 0,
	};

	function applyMode() {
		const zebra = 'zebra' === state.mode;
		tube.visible = ! zebra;
		headbox.visible = zebra;
		chain.visible = !! opts.chain;
		fabricBack.visible = zebra;
		front.alphaMap = zebra ? alpha : null;
		back.alphaMap = zebra ? alphaBack : null;
		front.needsUpdate = back.needsUpdate = true;
	}

	function layout() {
		const zebra = 'zebra' === state.mode;
		// Roller: the drop shortens as it rolls. Zebra: full drop, bands slide.
		const drop = zebra ? H + 0.05 : Math.max( 0.02, ( H + 0.05 ) * ( 1 - state.progress ) );
		fabric.scale.y = fabricBack.scale.y = drop;
		// One swatch tile ≈ 2.4 units, so the (non-seamless) photo mirrors at most
		// once across the width; the weave scale stays constant while rolling.
		const rep = drop / 2.4;
		[ front.map, back.map ].forEach( ( m ) => m && m.repeat.set( W / 2.4, rep ) );
		if ( zebra ) {
			// 0 → bands interleave (closed); 1 → bands align (light through sheer).
			alpha.offset.y = 0;
			alphaBack.offset.y = 0.5 - state.progress * 0.5;
		}
		bottomBar.position.set( 0, TOP - 0.11 - drop, 0.22 );
		tube.scale.set( 1 + state.progress * 0.35, 1, 1 + state.progress * 0.35 );
		front.opacity = back.opacity = state.fade;
	}

	let dirty = true;
	let raf = 0;
	let running = false;
	let last = performance.now();

	function frame( now ) {
		raf = 0;
		const dt = Math.min( 0.05, ( now - last ) / 1000 );
		last = now;
		// Ease progress + pointer parallax toward their targets.
		const k = 1 - Math.exp( -dt * 4.5 );
		state.progress += ( state.target - state.progress ) * k;
		state.rotY += ( state.pointerX * 0.16 - state.rotY ) * k;
		state.rotX += ( state.pointerY * 0.06 - state.rotX ) * k;
		rig.rotation.y = state.rotY;
		rig.rotation.x = state.rotX;
		layout();
		renderer.render( scene, camera );
		const moving =
			Math.abs( state.target - state.progress ) > 0.0005 ||
			Math.abs( state.pointerX * 0.16 - state.rotY ) > 0.0005 ||
			Math.abs( state.pointerY * 0.06 - state.rotX ) > 0.0005;
		if ( running && ( moving || dirty ) ) {
			dirty = false;
			raf = requestAnimationFrame( frame );
		}
	}
	function kick() {
		dirty = true;
		if ( running && ! raf ) {
			last = performance.now();
			raf = requestAnimationFrame( frame );
		}
	}

	function resize() {
		const w = container.clientWidth || 1;
		const h = container.clientHeight || w;
		renderer.setSize( w, h, false );
		camera.aspect = w / h;
		// Fit the window (plus margin) in view whatever the aspect ratio.
		const fitH = ( H + 1.2 ) / 2 / Math.tan( MathUtils.degToRad( camera.fov / 2 ) );
		const fitW = ( W + 1.4 ) / 2 / Math.tan( MathUtils.degToRad( camera.fov / 2 ) ) / camera.aspect;
		camera.position.z = Math.max( fitH, fitW );
		camera.updateProjectionMatrix();
		kick();
	}

	const canvas = renderer.domElement;
	canvas.className = 'blackroll-blind3d__canvas';
	canvas.setAttribute( 'aria-hidden', 'true' );
	container.appendChild( canvas );

	const first = await loadTexture( opts.texture );
	front.map = first;
	back.map = first;
	front.color = new Color( 0xffffff );
	applyMode();
	resize();

	const ro = 'ResizeObserver' in window ? new ResizeObserver( resize ) : null;
	if ( ro ) {
		ro.observe( container );
	}
	const io =
		'IntersectionObserver' in window
			? new IntersectionObserver( ( entries ) => {
					running = entries.some( ( e ) => e.isIntersecting );
					kick();
			  } )
			: null;
	if ( io ) {
		io.observe( container );
	} else {
		running = true;
	}

	function onPointer( e ) {
		const r = container.getBoundingClientRect();
		state.pointerX = MathUtils.clamp( ( e.clientX - r.left ) / r.width - 0.5, -0.5, 0.5 );
		state.pointerY = MathUtils.clamp( ( e.clientY - r.top ) / r.height - 0.5, -0.5, 0.5 );
		kick();
	}
	if ( window.matchMedia( '(hover: hover)' ).matches ) {
		window.addEventListener( 'pointermove', onPointer, { passive: true } );
	}

	return {
		setProgress( p ) {
			state.target = MathUtils.clamp( p, 0, 1 );
			kick();
		},
		async setTexture( src ) {
			const tex = await loadTexture( src );
			// Quick dip-and-swap so the colour change reads as intentional.
			state.fade = 0.25;
			layout();
			front.map = back.map = tex;
			front.needsUpdate = back.needsUpdate = true;
			const start = performance.now();
			const step = ( now ) => {
				state.fade = Math.min( 1, 0.25 + ( ( now - start ) / 350 ) * 0.75 );
				kick();
				if ( state.fade < 1 ) {
					requestAnimationFrame( step );
				}
			};
			requestAnimationFrame( step );
		},
		setMode( m ) {
			state.mode = 'zebra' === m ? 'zebra' : 'roller';
			applyMode();
			kick();
		},
		destroy() {
			running = false;
			if ( ro ) {
				ro.disconnect();
			}
			if ( io ) {
				io.disconnect();
			}
			window.removeEventListener( 'pointermove', onPointer );
			renderer.dispose();
			canvas.remove();
		},
	};
}
