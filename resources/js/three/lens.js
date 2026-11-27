import * as THREE from 'three';

/** Eye-chart texture drawn on a canvas (brand storytelling backdrop). */
function chartTexture(w, h) {
    const c = document.createElement('canvas'); c.width = w; c.height = h;
    const g = c.getContext('2d');
    g.fillStyle = '#101113'; g.fillRect(0, 0, w, h);
    const rows = ['E', 'F P', 'T O Z', 'L P E D', 'P E C F D', 'E D F C Z P', 'F E L O P Z D', 'D E F P O T E C', 'L E F O D P C T'];
    let y = h * 0.1;
    rows.forEach((r, i) => {
        const size = Math.max(h * 0.16 * Math.pow(0.74, i), 14);
        g.font = `600 ${size}px Georgia, serif`;
        g.fillStyle = i % 2 ? 'rgba(232,211,162,0.85)' : 'rgba(255,255,255,0.88)';
        g.textAlign = 'center'; g.textBaseline = 'middle';
        g.fillText(r.split(' ').join('   '), w * 0.68, y + size / 2);
        y += size * 1.35;
    });
    g.font = `500 ${h * 0.022}px Helvetica, Arial, sans-serif`;
    g.fillStyle = 'rgba(255,255,255,0.35)';
    g.fillText('O P T I C   V A L L E Y   ·   P R E C I S I O N   L E N S E S', w * 0.68, h * 0.94);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

export function mountLens(container) {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const mobile = window.matchMedia('(max-width: 767px)').matches;
    const renderer = new THREE.WebGLRenderer({ antialias: false, alpha: false });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, mobile ? 1.25 : 1.75));
    renderer.domElement.setAttribute('aria-hidden', 'true');
    container.appendChild(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    let tex = chartTexture(1600, 1000);

    const uniforms = {
        uTex: { value: tex }, uRes: { value: new THREE.Vector2(1, 1) }, uMouse: { value: new THREE.Vector2(0.5, 0.5) },
        uRadius: { value: mobile ? 0.3 : 0.24 }, uTime: { value: 0 }, uTexAspect: { value: 1.6 },
    };
    const mat = new THREE.ShaderMaterial({
        uniforms,
        vertexShader: 'varying vec2 vUv; void main(){ vUv=uv; gl_Position=vec4(position.xy,0.,1.); }',
        fragmentShader: `
            precision highp float; varying vec2 vUv;
            uniform sampler2D uTex; uniform vec2 uRes; uniform vec2 uMouse; uniform float uRadius; uniform float uTime; uniform float uTexAspect;
            vec2 cover(vec2 uv){ float sa=uRes.x/uRes.y; vec2 s = sa>uTexAspect ? vec2(1., uTexAspect/sa) : vec2(sa/uTexAspect, 1.); return (uv-.5)*s+.5; }
            void main(){
                float aspect=uRes.x/uRes.y;
                vec2 p=vUv; vec2 d=p-uMouse; d.x*=aspect; float r=length(d);
                float R=uRadius; float inside=smoothstep(R,R-.004,r);
                // magnification + barrel refraction inside the lens
                float k = 1. - .42*(1.-pow(r/R,2.));
                vec2 dir = (p-uMouse)*k;
                vec2 uvIn = uMouse + dir;
                float ca = .006*(r/R);
                vec3 lensCol = vec3(texture2D(uTex,cover(uvIn+dir*ca)).r, texture2D(uTex,cover(uvIn)).g, texture2D(uTex,cover(uvIn-dir*ca)).b);
                vec3 base = texture2D(uTex,cover(p)).rgb*.42;
                // caustic shimmer & edge highlight
                float ring = smoothstep(.012,0.,abs(r-R+.006));
                float caust = .5+.5*sin(r*120.-uTime*1.6)*sin(atan(d.y,d.x)*6.+uTime*.6);
                float glow = smoothstep(R*1.6,R,r)*(1.-inside);
                vec3 champagne=vec3(.86,.72,.48);
                vec3 col = mix(base, lensCol*1.08 + champagne*caust*.05*(r/R), inside);
                col += champagne*ring*.9 + champagne*glow*.12;
                // specular sweep
                vec2 sp = d - vec2(-.35,.35)*R; col += inside*pow(max(0.,1.-length(sp)/(R*.55)),3.)*.35;
                gl_FragColor=vec4(col,1.);
            }`,
    });
    scene.add(new THREE.Mesh(new THREE.PlaneGeometry(2, 2), mat));

    // GPU particles drifting, gently pushed by the lens
    const n = mobile ? 160 : 420;
    const pos = new Float32Array(n * 3), seed = new Float32Array(n);
    for (let i = 0; i < n; i++) { pos[i * 3] = Math.random() * 2 - 1; pos[i * 3 + 1] = Math.random() * 2 - 1; seed[i] = Math.random() * 10; }
    const pg = new THREE.BufferGeometry();
    pg.setAttribute('position', new THREE.BufferAttribute(pos, 3)); pg.setAttribute('seed', new THREE.BufferAttribute(seed, 1));
    const pu = { uTime: uniforms.uTime, uMouse: uniforms.uMouse, uRes: uniforms.uRes, uPx: { value: renderer.getPixelRatio() } };
    const pm = new THREE.ShaderMaterial({
        uniforms: pu, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
        vertexShader: `attribute float seed; uniform float uTime; uniform vec2 uMouse; uniform vec2 uRes; uniform float uPx; varying float vA;
            void main(){ vec2 p=position.xy; p.y=mod(p.y+uTime*.02*(.5+fract(seed))+1.,2.)-1.; p.x+=sin(uTime*.3+seed)*.03;
            vec2 m=uMouse*2.-1.; vec2 d=p-m; d.x*=uRes.x/uRes.y; float l=length(d); p+= normalize(d+1e-4)*.06*smoothstep(.45,0.,l);
            gl_Position=vec4(p,0.,1.); gl_PointSize=(1.5+fract(seed*3.)*2.5)*uPx; vA=.25+.6*fract(seed*5.);} `,
        fragmentShader: 'varying float vA; void main(){ float d=length(gl_PointCoord-.5); if(d>.5) discard; gl_FragColor=vec4(.9,.78,.55, smoothstep(.5,0.,d)*vA); }',
    });
    scene.add(new THREE.Points(pg, pm));

    const target = new THREE.Vector2(0.68, 0.52);
    const onMove = (e) => {
        const r = container.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width, y = 1 - (e.clientY - r.top) / r.height;
        if (x >= 0 && x <= 1 && y >= 0 && y <= 1) target.set(x, y);
    };
    container.addEventListener('pointermove', onMove, { passive: true });

    const resize = () => {
        const w = container.clientWidth, h = container.clientHeight;
        renderer.setSize(w, h, false); uniforms.uRes.value.set(w, h);
    };
    const ro = new ResizeObserver(resize); ro.observe(container); resize();

    let visible = false, raf = 0, t = 0, last = performance.now();
    const io = new IntersectionObserver(([e]) => { visible = e.isIntersecting; if (visible && !raf) loop(); }, { threshold: 0.05 });
    io.observe(container);
    function loop() {
        raf = 0;
        if (!visible || document.hidden) return;
        const now = performance.now(); const dt = Math.min((now - last) / 1000, 0.05); last = now;
        if (!reduce) t += dt;
        if (!reduce) { // idle drift when the pointer is not over the section
            target.x += Math.sin(t * 0.4) * 0.0006; target.y += Math.cos(t * 0.33) * 0.0005;
        }
        uniforms.uMouse.value.lerp(target, reduce ? 1 : 0.07);
        uniforms.uTime.value = t;
        renderer.render(scene, camera);
        raf = requestAnimationFrame(loop);
    }
    loop();
    requestAnimationFrame(() => container.classList.add('is-ready'));

    return () => {
        cancelAnimationFrame(raf); io.disconnect(); ro.disconnect(); container.removeEventListener('pointermove', onMove);
        tex.dispose(); mat.dispose(); pg.dispose(); pm.dispose(); renderer.dispose(); renderer.domElement.remove();
    };
}
