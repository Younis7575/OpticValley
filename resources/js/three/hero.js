import * as THREE from 'three';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';
import { buildEyewear, disposeTree } from './eyewear.js';

/** Cinematic floating-eyewear hero. Returns a cleanup function. */
export function mountHero(container) {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const mobile = window.matchMedia('(max-width: 767px)').matches;
    const renderer = new THREE.WebGLRenderer({ antialias: !mobile, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, mobile ? 1.5 : 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;
    container.appendChild(renderer.domElement);
    renderer.domElement.setAttribute('aria-hidden', 'true');

    const scene = new THREE.Scene();
    const pmrem = new THREE.PMREMGenerator(renderer);
    const envRT = pmrem.fromScene(new RoomEnvironment(), 0.04);
    scene.environment = envRT.texture;

    const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 100);
    camera.position.set(0, 0.2, 9.5);

    const key = new THREE.DirectionalLight(0xfff1dc, 2.4); key.position.set(4, 5, 6); scene.add(key);
    const rim = new THREE.DirectionalLight(0xc9a66b, 3.0); rim.position.set(-6, 2, -4); scene.add(rim);
    scene.add(new THREE.AmbientLight(0xffffff, 0.25));

    const glasses = buildEyewear();
    glasses.scale.setScalar(mobile ? 0.82 : 1);
    glasses.rotation.set(0.12, -0.5, 0);
    scene.add(glasses);

    // shadow disc
    const shadow = new THREE.Mesh(new THREE.CircleGeometry(2.6, 48), new THREE.MeshBasicMaterial({ color: 0x000000, transparent: true, opacity: 0.35 }));
    shadow.rotation.x = -Math.PI / 2; shadow.position.y = -1.7; shadow.scale.set(1.4, 0.5, 1);
    scene.add(shadow);

    // sparse champagne dust
    const count = mobile ? 120 : 320;
    const pos = new Float32Array(count * 3), seed = new Float32Array(count);
    for (let i = 0; i < count; i++) {
        pos[i * 3] = (Math.random() - 0.5) * 14; pos[i * 3 + 1] = (Math.random() - 0.5) * 8; pos[i * 3 + 2] = (Math.random() - 0.5) * 6 - 1;
        seed[i] = Math.random() * 10;
    }
    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    pGeo.setAttribute('seed', new THREE.BufferAttribute(seed, 1));
    const pMat = new THREE.ShaderMaterial({
        transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
        uniforms: { uTime: { value: 0 }, uPx: { value: renderer.getPixelRatio() } },
        vertexShader: `attribute float seed; uniform float uTime; uniform float uPx; varying float vA;
            void main(){ vec3 p=position; p.y+=sin(uTime*.25+seed)*.35; p.x+=cos(uTime*.18+seed*1.3)*.25;
            vec4 mv=modelViewMatrix*vec4(p,1.); gl_Position=projectionMatrix*mv;
            gl_PointSize=(2.2+fract(seed)*3.)*uPx*(6./-mv.z); vA=.35+.65*fract(seed*7.3);} `,
        fragmentShader: `varying float vA; void main(){ float d=length(gl_PointCoord-.5); if(d>.5) discard;
            gl_FragColor=vec4(0.86,0.72,0.48, smoothstep(.5,0.,d)*vA*.8);} `,
    });
    const dust = new THREE.Points(pGeo, pMat);
    scene.add(dust);

    const target = { x: 0, y: 0 };
    const onMove = (e) => {
        const r = container.getBoundingClientRect();
        const cx = (e.touches ? e.touches[0].clientX : e.clientX), cy = (e.touches ? e.touches[0].clientY : e.clientY);
        target.x = ((cx - r.left) / r.width - 0.5) * 2; target.y = ((cy - r.top) / r.height - 0.5) * 2;
    };
    window.addEventListener('pointermove', onMove, { passive: true });

    const resize = () => {
        const w = container.clientWidth, h = container.clientHeight;
        renderer.setSize(w, h, false); camera.aspect = w / h;
        camera.position.x = w > 1024 ? -2.2 : 0; // leave room for copy on the left
        camera.updateProjectionMatrix();
    };
    const ro = new ResizeObserver(resize); ro.observe(container); resize();

    let visible = true, raf = 0, last = performance.now(), t = 0;
    const io = new IntersectionObserver(([e]) => { visible = e.isIntersecting; if (visible && !raf) loop(); }, { threshold: 0.01 });
    io.observe(container);
    const onVis = () => { if (!document.hidden && visible && !raf) loop(); };
    document.addEventListener('visibilitychange', onVis);

    function loop() {
        raf = 0;
        if (!visible || document.hidden) return;
        const now = performance.now(); const dt = Math.min((now - last) / 1000, 0.05); last = now;
        if (!reduce) t += dt;
        const idle = reduce ? 0 : Math.sin(t * 0.35) * 0.35;
        glasses.rotation.y += ((-0.45 + idle + target.x * 0.35) - glasses.rotation.y) * 0.04;
        glasses.rotation.x += ((0.1 + target.y * 0.18) - glasses.rotation.x) * 0.04;
        glasses.position.y = reduce ? 0 : Math.sin(t * 0.9) * 0.12;
        shadow.material.opacity = 0.32 - glasses.position.y * 0.4;
        pMat.uniforms.uTime.value = t;
        dust.rotation.y = target.x * 0.05;
        renderer.render(scene, camera);
        raf = requestAnimationFrame(loop);
    }
    renderer.compile(scene, camera);
    loop();
    requestAnimationFrame(() => container.classList.add('is-ready'));

    return () => {
        cancelAnimationFrame(raf); io.disconnect(); ro.disconnect();
        window.removeEventListener('pointermove', onMove); document.removeEventListener('visibilitychange', onVis);
        disposeTree(scene); envRT.dispose(); pmrem.dispose(); renderer.dispose(); renderer.domElement.remove();
    };
}
