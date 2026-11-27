import * as THREE from 'three';

/**
 * Procedural, representative eyewear model (not a specific product).
 * Wayfarer-style rims, tinted lenses, keyhole bridge, hinged temples.
 */
function rimPath(w, h, r, lift = 0.08) {
    const s = new THREE.Shape();
    const x0 = -w / 2, y0 = -h / 2;
    s.moveTo(x0 + r, y0);
    s.lineTo(x0 + w - r, y0);
    s.quadraticCurveTo(x0 + w, y0, x0 + w, y0 + r);
    s.lineTo(x0 + w + lift * 0.4, y0 + h - r);
    s.quadraticCurveTo(x0 + w + lift * 0.4, y0 + h + lift, x0 + w - r, y0 + h + lift);
    s.lineTo(x0 + r, y0 + h);
    s.quadraticCurveTo(x0, y0 + h, x0, y0 + h - r);
    s.lineTo(x0, y0 + r);
    s.quadraticCurveTo(x0, y0, x0 + r, y0);
    return s;
}

export function buildEyewear({ frameColor = 0x050505, lensTint = 0x6b4424, accent = 0xd8b67a } = {}) {
    const group = new THREE.Group();
    const frameMat = new THREE.MeshPhysicalMaterial({ color: frameColor, roughness: 0.35, metalness: 0.0, clearcoat: 1, clearcoatRoughness: 0.05, envMapIntensity: 0.55 });
    const lensMat = new THREE.MeshPhysicalMaterial({ color: lensTint, roughness: 0.02, metalness: 0, transparent: true, opacity: 0.78, ior: 1.5, clearcoat: 1, reflectivity: 0.9, envMapIntensity: 1.4 });
    const metalMat = new THREE.MeshStandardMaterial({ color: accent, roughness: 0.25, metalness: 1 });

    const W = 1.42, H = 1.0, R = 0.26, gap = 0.36;
    const lensShape = rimPath(W, H, R);
    const outer = rimPath(W + 0.2, H + 0.2, R + 0.08);
    outer.holes.push(new THREE.Path(lensShape.getPoints(48)));
    const rimGeo = new THREE.ExtrudeGeometry(outer, { depth: 0.12, bevelEnabled: true, bevelSize: 0.03, bevelThickness: 0.03, bevelSegments: 3, curveSegments: 32 });
    rimGeo.center();
    const lensGeo = new THREE.ExtrudeGeometry(lensShape, { depth: 0.02, bevelEnabled: false, curveSegments: 32 });
    lensGeo.center();

    [-1, 1].forEach((side) => {
        const rim = new THREE.Mesh(rimGeo, frameMat);
        rim.position.x = side * (W / 2 + gap / 2 + 0.1);
        rim.scale.x = side;
        rim.rotation.y = side * -0.06;
        group.add(rim);
        const lens = new THREE.Mesh(lensGeo, lensMat);
        lens.position.copy(rim.position);
        lens.scale.x = side;
        lens.rotation.y = rim.rotation.y;
        group.add(lens);

        // hinge stud
        const stud = new THREE.Mesh(new THREE.CylinderGeometry(0.035, 0.035, 0.06, 16), metalMat);
        stud.rotation.x = Math.PI / 2;
        stud.position.set(side * (W + gap / 2 + 0.12), 0.34, 0.08);
        group.add(stud);

        // temple arm
        const armShape = new THREE.Shape();
        armShape.moveTo(0, 0); armShape.lineTo(2.6, 0.02); armShape.quadraticCurveTo(3.0, 0.0, 3.15, -0.35);
        armShape.lineTo(3.08, -0.38); armShape.quadraticCurveTo(2.9, -0.08, 2.6, -0.1); armShape.lineTo(0, -0.14); armShape.lineTo(0, 0);
        const armGeo = new THREE.ExtrudeGeometry(armShape, { depth: 0.06, bevelEnabled: true, bevelSize: 0.015, bevelThickness: 0.015, bevelSegments: 2, curveSegments: 16 });
        const arm = new THREE.Mesh(armGeo, frameMat);
        arm.rotation.y = side > 0 ? -Math.PI / 2 + 0.08 : Math.PI / 2 - 0.08;
        arm.position.set(side * (W + gap / 2 + 0.15), 0.4, 0.0);
        if (side < 0) arm.position.z = 0.06;
        group.add(arm);
        // metal accent on temple
        const pin = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.06, 0.55), metalMat);
        pin.position.set(side * (W + gap / 2 + 0.2), 0.34, -0.2);
        group.add(pin);
    });

    // keyhole bridge
    const bridgeCurve = new THREE.QuadraticBezierCurve3(new THREE.Vector3(-gap / 2 - 0.12, 0.28, 0), new THREE.Vector3(0, 0.5, 0.04), new THREE.Vector3(gap / 2 + 0.12, 0.28, 0));
    group.add(new THREE.Mesh(new THREE.TubeGeometry(bridgeCurve, 32, 0.07, 12, false), frameMat));
    const topBar = new THREE.QuadraticBezierCurve3(new THREE.Vector3(-gap / 2 - 0.2, 0.56, 0.02), new THREE.Vector3(0, 0.6, 0.06), new THREE.Vector3(gap / 2 + 0.2, 0.56, 0.02));
    group.add(new THREE.Mesh(new THREE.TubeGeometry(topBar, 32, 0.05, 12, false), frameMat));

    return group;
}

export function disposeTree(root) {
    root.traverse((o) => {
        if (o.geometry) o.geometry.dispose();
        if (o.material) (Array.isArray(o.material) ? o.material : [o.material]).forEach((m) => m.dispose());
    });
}
