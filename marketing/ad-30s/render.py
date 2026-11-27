"""
OpticValley — 30s luxury commercial renderer.
Builds every frame from genuine OpticValley assets (logo, real product photos,
store image, live-site screenshots). Usage:  python3 render.py 1920 1080 out/frames_169
"""
import math, os, sys, random
import numpy as np
from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageEnhance

W, H = int(sys.argv[1]), int(sys.argv[2])
OUT = sys.argv[3]
LANG = os.environ.get('AD_LANG', 'en')
FPS, DUR = 30, 30
N = FPS * DUR
ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
A = lambda p: os.path.join(ROOT, p)
os.makedirs(OUT, exist_ok=True)
PORTRAIT = H > W
U = min(W, H) / 1080.0  # layout unit

GOLD = (232, 211, 162); CHAMP = (182, 154, 112); ONYX = (16, 17, 19); IVORY = (247, 246, 242)
SERIF = '/System/Library/Fonts/Supplemental/Georgia.ttf'
SANS = '/System/Library/Fonts/Helvetica.ttc'
random.seed(7); np.random.seed(7)

def font(path, size): return ImageFont.truetype(path, max(8, int(size)))
def clamp(x, a=0.0, b=1.0): return max(a, min(b, x))
def ease(t): t = clamp(t); return 1 - (1 - t) ** 3
def ease_io(t): t = clamp(t); return t * t * (3 - 2 * t)
def seg(f, a, b): return clamp((f - a) / max(1, (b - a)))

def load(p, mode='RGBA'): return Image.open(A(p)).convert(mode)
def trim(im):
    bb = im.split()[3].point(lambda v: 255 if v > 20 else 0).getbbox()
    return im.crop(bb) if bb else im

LOGO = trim(load('public/images/logo.png'))
LOGO_L = trim(load('public/images/logo-light.png'))
MARK_L = trim(load('public/images/logo-mark-light.png'))
def tint_lenses(im):
    # Replace the washed-out lens interior with a smooth smoked tint for dark scenes.
    a = np.asarray(im).astype(float); rgb = a[..., :3]; al = a[..., 3] / 255
    mn = rgb.min(2); sat = rgb.max(2) - mn
    core = np.asarray(Image.fromarray((np.clip((al - 0.05) * 3, 0, 1) * 255).astype('uint8')).filter(ImageFilter.MaxFilter(9))).astype(float) / 255
    # lens = inside the frame outline (dilated alpha) but where the overlay itself is mostly transparent
    lens = np.clip(core - al * 1.4, 0, 1)
    lens = np.maximum(lens, np.clip((mn - 110) / 70, 0, 1) * np.clip((55 - sat) / 35, 0, 1) * (al > 0.05))
    lens = np.asarray(Image.fromarray((lens * 255).astype('uint8')).filter(ImageFilter.GaussianBlur(6))).astype(float) / 255
    tint = np.array([38, 34, 30], float)
    yy = np.linspace(0, 1, a.shape[0])[:, None, None]
    tint_img = tint + (1 - yy) * 40  # subtle top highlight gradient
    out = rgb * (1 - lens[..., None]) + tint_img * lens[..., None]
    na = np.maximum(al * (1 - lens), lens * 0.82)
    return Image.fromarray(np.dstack([out, na * 255]).clip(0, 255).astype('uint8'), 'RGBA')

TRYON = {c: tint_lenses(trim(load(f'database/seeders/assets/baby-grip-tryon/{c}.png'))) for c in ['TR25005', 'TR25004', 'TR25001', 'TR25012', 'TR25002']}
PROD = {
    'TR25005': ('Baby Grip TR25005', 'Black & Blue Wayfarer', trim(load('database/seeders/assets/baby-grip/TR25005.webp'))),
    'TR25004': ('Baby Grip TR25004', 'Cherry Red Square', trim(load('database/seeders/assets/baby-grip/TR25004.webp'))),
    '1802': ('Marie Flower 1802', 'Royal Blue Rectangle', trim(load('database/seeders/assets/marie-flower/1802.webp'))),
    'TR25012': ('Baby Grip TR25012', 'Blush Pink Square', trim(load('database/seeders/assets/baby-grip/TR25012.webp'))),
    '3021': ('Marie Flower 3021', 'Amber Round', trim(load('database/seeders/assets/marie-flower/3021.webp'))),
}
STORE = load('public/images/hero/store-front.webp', 'RGB')
SITE_TRYON = load('marketing/ad-30s/assets/site-tryon.jpg', 'RGB')
SITE_PRODUCT = load('marketing/ad-30s/assets/site-product.jpg', 'RGB')

def fit(im, w, h):
    im = im.copy(); im.thumbnail((int(w), int(h)), Image.LANCZOS); return im

def paste_center(base, im, cx, cy, alpha=1.0):
    if alpha <= 0: return
    if alpha < 1:
        im = im.copy(); a = im.split()[3].point(lambda v: int(v * alpha)); im.putalpha(a)
    base.alpha_composite(im, (int(cx - im.width / 2), int(cy - im.height / 2)))

def text_tracked(draw, xy, s, fnt, fill, tracking=0.0, anchor='mm'):
    """Draws letter-spaced text centred on xy (tracking in em)."""
    widths = [draw.textlength(ch, font=fnt) for ch in s]
    sp = fnt.size * tracking
    total = sum(widths) + sp * (len(s) - 1)
    x = xy[0] - total / 2 if anchor[0] == 'm' else xy[0]
    for ch, w in zip(s, widths):
        draw.text((x, xy[1]), ch, font=fnt, fill=fill, anchor='lm')
        x += w + sp
    return total

URDU = {
    'SEE THE WORLD DIFFERENTLY.': 'دنیا کو نئے انداز سے دیکھیں',
    'STYLE THAT DEFINES YOU.': 'وہ انداز جو آپ کی پہچان ہے',
    'DISCOVER YOUR PERFECT FRAME.': 'اپنا بہترین فریم تلاش کریں',
    'PRECISION.  COMFORT.  CONFIDENCE.': 'درستگی  ۔  آرام  ۔  اعتماد',
    'Luxury Meets Technology.': 'لگژری اور ٹیکنالوجی کا حسین امتزاج',
    'FIND YOUR PERFECT VISION.': 'اپنا پرفیکٹ چشمہ آج ہی چُنیں',
    'SHOP NOW': 'ابھی خریدیں',
}
_UR_CACHE = {}
URDU_BIN = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'out', 'urdu')
def urdu_img(text, size, color, bold=True):
    key = (text, int(size), color, bold)
    if key not in _UR_CACHE:
        import subprocess, tempfile
        if not os.path.exists(URDU_BIN):
            subprocess.run(['swiftc', '-O', os.path.join(os.path.dirname(os.path.abspath(__file__)), 'urdu.swift'), '-o', URDU_BIN], check=True)
        tmp = tempfile.NamedTemporaryFile(suffix='.png', delete=False).name
        hexc = '%02x%02x%02x' % color
        subprocess.run([URDU_BIN, text, 'NotoNastaliqUrdu-Bold' if bold else 'NotoNastaliqUrdu', str(int(size)), hexc, tmp], check=True)
        im = Image.open(tmp).convert('RGBA'); os.remove(tmp)
        bb = im.split()[3].getbbox(); _UR_CACHE[key] = im.crop(bb) if bb else im
    return _UR_CACHE[key]

def overlay_text(base, lines, cy, alpha, color=(255, 255, 255)):
    if LANG == 'ur' and lines and lines[0][0] in URDU:
        s0, fp, sz = lines[0][:3]; col = lines[0][4] if len(lines[0]) > 4 else color
        im = urdu_img(URDU[s0], sz * U * 1.25, col)
        maxw = W * 0.9
        if im.width > maxw: im = im.resize((int(maxw), int(im.height * maxw / im.width)), Image.LANCZOS)
        paste_center(base, im, W / 2, cy, alpha)
        return
    return _overlay_text_en(base, lines, cy, alpha, color)

def _overlay_text_en(base, lines, cy, alpha, color=(255, 255, 255)):
    """lines: list of (text, fontpath, size, tracking, color?)"""
    if alpha <= 0: return
    layer = Image.new('RGBA', base.size, (0, 0, 0, 0)); d = ImageDraw.Draw(layer)
    y = cy
    for ln in lines:
        s, fp, sz, tr = ln[:4]; col = ln[4] if len(ln) > 4 else color
        f = font(fp, sz * U)
        text_tracked(d, (W / 2, y), s, f, col + (int(255 * alpha),), tr)
        y += sz * U * 1.45
    base.alpha_composite(layer)

def vignette(strength=0.6):
    y, x = np.ogrid[:H, :W]
    r = np.sqrt(((x - W / 2) / (W / 2)) ** 2 + ((y - H / 2) / (H / 2)) ** 2)
    v = 1 - strength * np.clip(r - 0.35, 0, 1) ** 1.6
    return v[..., None]
VIG = vignette(0.75)

def radial(color_in, color_out, cx=0.5, cy=0.45, rad=0.9):
    y, x = np.ogrid[:H, :W]
    r = np.sqrt(((x - W * cx) / W) ** 2 + ((y - H * cy) / H) ** 2) / rad
    t = np.clip(r, 0, 1)[..., None]
    a = np.array(color_in, float); b = np.array(color_out, float)
    return (a * (1 - t) + b * t)
BG_DARK = radial((34, 30, 26), ONYX, rad=0.75)
BG_IVORY = radial((255, 255, 255), (236, 232, 224), rad=1.1)

# ---- particles (champagne dust) ----
P = 140
px = np.random.rand(P) * W; py = np.random.rand(P) * H; ps = np.random.rand(P); pph = np.random.rand(P) * 6.28
def particles(f, strength=1.0):
    layer = Image.new('RGBA', (W, H), (0, 0, 0, 0)); d = ImageDraw.Draw(layer)
    t = f / FPS
    for i in range(P):
        x = (px[i] + math.sin(t * 0.3 + pph[i]) * 40 * U) % W
        y = (py[i] - t * (8 + 18 * ps[i]) * U) % H
        r = (1.2 + 2.8 * ps[i]) * U
        a = int((60 + 140 * ps[i]) * strength * (0.6 + 0.4 * math.sin(t * 1.3 + pph[i])))
        d.ellipse((x - r, y - r, x + r, y + r), fill=GOLD + (max(0, a),))
    return layer.filter(ImageFilter.GaussianBlur(1.2 * U))

def soft_shadow(w, alpha):
    sh = Image.new('RGBA', (int(w * 1.3), int(w * 0.32)), (0, 0, 0, 0))
    ImageDraw.Draw(sh).ellipse((sh.width * 0.12, sh.height * 0.35, sh.width * 0.88, sh.height * 0.65), fill=(0, 0, 0, alpha))
    return sh.filter(ImageFilter.GaussianBlur(w * 0.05))

def to_img(arr): return Image.fromarray(np.clip(arr, 0, 255).astype('uint8')).convert('RGBA')

def light_beam(f, a, b, base_alpha=0.55):
    """Diagonal soft beam sweeping left→right between frames a..b."""
    t = ease_io(seg(f, a, b))
    y, x = np.ogrid[:H, :W]
    pos = -0.4 * W + t * 1.8 * W
    dist = np.abs((x - pos) + (y - H / 2) * 0.45)
    beam = np.exp(-(dist / (W * 0.12)) ** 2) * base_alpha
    return beam[..., None]

# ================= SCENES =================
def scene1(f):  # 0-5s first impression
    arr = BG_DARK * (0.15 + 0.85 * ease(seg(f, 0, 70)))
    beam = light_beam(f, 10, 140, 0.35)
    arr = arr + beam * np.array(GOLD) * 0.35
    img = to_img(arr * VIG)
    img.alpha_composite(particles(f, ease(seg(f, 15, 80))))
    g = TRYON['TR25005']
    s = 0.62 + 0.14 * ease_io(seg(f, 0, 150))
    gw = (W * (0.9 if PORTRAIT else 0.55)) * s
    gi = fit(g, gw, H * 0.5)
    reveal = ease(seg(f, 18, 85))
    # brighten where the beam passes
    gi2 = ImageEnhance.Brightness(gi).enhance(0.55 + 0.75 * reveal)
    yoff = math.sin(f / FPS * 1.6) * 8 * U
    # reflection/shadow
    paste_center(img, soft_shadow(gi.width, 170), W / 2, H * 0.44 + gi.height * 0.62, reveal)
    paste_center(img, gi2, W / 2, H * (0.44 if not PORTRAIT else 0.42) + yoff, reveal)
    a = ease(seg(f, 72, 100)) * (1 - seg(f, 140, 150))
    overlay_text(img, [('SEE THE WORLD DIFFERENTLY.', SERIF, 58 if not PORTRAIT else 50, 0.12 - 0.06 * ease(seg(f, 72, 140)))], H * (0.8 if not PORTRAIT else 0.74), a)
    return img

def scene2(f):  # 5-10s fashion statement — macro close-ups of real frames
    t = seg(f, 150, 300)
    arr = BG_DARK * 0.9 + light_beam(f, 150, 300, 0.3) * np.array(GOLD) * 0.3
    img = to_img(arr * VIG)
    img.alpha_composite(particles(f, 0.6))
    # first half: extreme close-up pan across TR25004; second half: pull back
    g = TRYON['TR25004']
    zoom = 2.6 - 1.75 * ease_io(seg(f, 150, 285))
    gi = fit(g, W * 0.6 * zoom * (1.6 if PORTRAIT else 1), H * 2)
    pan = math.sin((1 - ease_io(seg(f, 150, 285))) * math.pi * 0.5) * gi.width * 0.22
    paste_center(img, gi, W / 2 + pan, H * 0.46, ease(seg(f, 150, 172)) * (1 - seg(f, 292, 300)))
    a = ease(seg(f, 200, 228)) * (1 - seg(f, 290, 300))
    overlay_text(img, [('STYLE THAT DEFINES YOU.', SERIF, 58 if not PORTRAIT else 50, 0.08)], H * (0.84 if not PORTRAIT else 0.78), a)
    return img

S3_ORDER = ['1802', 'TR25005', '3021', 'TR25012']
def scene3(f):  # 10-16s product experience
    img = to_img(BG_IVORY)
    local = f - 300; per = 45
    idx = min(3, local // per); lt = (local % per) / per if idx < 3 or local < per * 4 else 1
    for k in range(max(0, idx - 1), idx + 1):
        code = S3_ORDER[k]; name, sub, pim = PROD[code]
        kt = (local - k * per) / per
        enter = ease(clamp(kt * 2.2)); exit_ = ease(clamp((kt - 0.82) / 0.4)) if k < 3 else 0
        if kt < 0: continue
        x = W / 2 + (1 - enter) * W * 0.55 - exit_ * W * 0.55
        squash = 0.35 + 0.65 * math.cos((1 - enter) * 1.2) if enter < 1 else 1 - 0.04 * math.sin(kt * 3)
        gi = fit(pim, W * (0.82 if PORTRAIT else 0.5), H * 0.42)
        gi = gi.resize((max(1, int(gi.width * clamp(squash, 0.2, 1))), gi.height), Image.LANCZOS)
        alpha = enter * (1 - exit_)
        paste_center(img, soft_shadow(gi.width, 60), x, H * 0.44 + gi.height * 0.6, alpha)
        paste_center(img, gi, x, H * 0.44 + math.sin(kt * 3.1) * 6 * U, alpha)
        lab = Image.new('RGBA', (W, H), (0, 0, 0, 0)); d = ImageDraw.Draw(lab)
        la = int(255 * alpha * ease(clamp(kt * 3 - 0.4)))
        d.text((x, H * (0.74 if not PORTRAIT else 0.66)), name.upper(), font=font(SANS, 22 * U), fill=(143, 106, 47, la), anchor='mm')
        d.text((x, H * (0.74 if not PORTRAIT else 0.66) + 34 * U), sub, font=font(SERIF, 30 * U), fill=(21, 21, 21, la), anchor='mm')
        img.alpha_composite(lab)
    a = ease(seg(f, 305, 330)) * (1 - seg(f, 470, 480))
    overlay_text(img, [('DISCOVER YOUR PERFECT FRAME.', SERIF, 52 if not PORTRAIT else 44, 0.08, (21, 21, 21))], H * (0.15 if not PORTRAIT else 0.16), a)
    return img

# eye-chart texture for the lens scene
def eye_chart():
    c = Image.new('RGB', (W, H), ONYX); d = ImageDraw.Draw(c)
    rows = ['E', 'F P', 'T O Z', 'L P E D', 'P E C F D', 'E D F C Z P', 'F E L O P Z D', 'D E F P O T E C']
    y = H * 0.08
    for i, r in enumerate(rows):
        sz = max(H * 0.15 * (0.74 ** i), 14)
        d.text((W / 2, y + sz / 2), '   '.join(r.split()), font=font(SERIF, sz), fill=(232, 211, 162) if i % 2 else (245, 245, 245), anchor='mm')
        y += sz * 1.38
    return np.asarray(c).astype(float)
CHART = None
YY, XX = np.mgrid[0:H, 0:W].astype(np.float32)
def scene4(f):  # 16-21s technology meets vision
    global CHART
    if f < 555:
        if CHART is None: CHART = eye_chart()
        t = seg(f, 480, 555)
        cx = W * (0.3 + 0.4 * ease_io(t)); cy = H * (0.42 + 0.06 * math.sin(t * 3))
        R = 0.26 * min(W, H)
        dx = XX - cx; dy = YY - cy; r = np.sqrt(dx * dx + dy * dy)
        inside = r < R
        k = np.where(inside, 1 - 0.45 * (1 - (r / R) ** 2), 1.0)
        sx = np.clip(cx + dx * k, 0, W - 1).astype(np.int32); sy = np.clip(cy + dy * k, 0, H - 1).astype(np.int32)
        ca = (r / R * 3).astype(np.int32) * inside
        out = np.empty_like(CHART)
        out[..., 0] = CHART[sy, np.clip(sx + ca, 0, W - 1), 0]
        out[..., 1] = CHART[sy, sx, 1]
        out[..., 2] = CHART[sy, np.clip(sx - ca, 0, W - 1), 2]
        out = np.where(inside[..., None], out * 1.05, CHART * 0.35)
        ring = np.exp(-((r - R) / (3 * U)) ** 2)[..., None]
        glow = np.exp(-np.clip(r - R, 0, None) / (60 * U))[..., None] * (~inside)[..., None]
        out = out + ring * np.array(GOLD) * 0.9 + glow * np.array(GOLD) * 0.08
        img = to_img(out * VIG)
        img.alpha_composite(particles(f, 0.7))
    else:
        img = to_img(BG_DARK * VIG)
        img.alpha_composite(particles(f, 0.5))
        t = seg(f, 555, 630)
        shot = SITE_TRYON if not PORTRAIT else SITE_PRODUCT
        sw = W * (0.78 if not PORTRAIT else 0.92) * (0.92 + 0.08 * ease_io(t))
        si = fit(shot.convert('RGBA'), sw, H * (0.7 if not PORTRAIT else 0.55))
        mask = Image.new('L', si.size, 0); ImageDraw.Draw(mask).rounded_rectangle((0, 0, si.width, si.height), radius=int(22 * U), fill=255); si.putalpha(mask)
        glow = Image.new('RGBA', (si.width + int(80 * U), si.height + int(80 * U)), (0, 0, 0, 0))
        ImageDraw.Draw(glow).rounded_rectangle((40 * U, 40 * U, glow.width - 40 * U, glow.height - 40 * U), radius=int(30 * U), fill=CHAMP + (110,))
        a = ease(seg(f, 556, 580))
        paste_center(img, glow.filter(ImageFilter.GaussianBlur(30 * U)), W / 2, H * 0.46, a)
        paste_center(img, si, W / 2, H * 0.46 + (1 - ease(seg(f, 556, 590))) * 60 * U, a)
    a = ease(seg(f, 490, 515)) * (1 - seg(f, 620, 630))
    overlay_text(img, [('PRECISION.  COMFORT.  CONFIDENCE.', SANS, 34 if not PORTRAIT else 30, 0.22, GOLD)], H * (0.9 if not PORTRAIT else 0.85), a)
    return img

def scene5(f):  # 21-26s brand reveal
    img = to_img(BG_DARK * 0.8 * VIG)
    img.alpha_composite(particles(f, 0.8))
    t = seg(f, 630, 780)
    g = TRYON['TR25001']
    gi = fit(g, W * (0.7 if PORTRAIT else 0.36) * (0.9 + 0.1 * ease_io(t)), H * 0.28)
    # orbit illusion: horizontal squash oscillation
    sq = 0.82 + 0.18 * math.cos(t * math.pi * 1.2)
    gi = gi.resize((max(1, int(gi.width * sq)), gi.height), Image.LANCZOS)
    paste_center(img, gi, W / 2, H * (0.3 if not PORTRAIT else 0.3), ease(seg(f, 632, 660)) * (1 - 0.5 * seg(f, 700, 760)))
    lg = fit(LOGO_L, W * (0.62 if PORTRAIT else 0.3), H * 0.34)
    rev = ease_io(seg(f, 670, 725))
    m = Image.new('L', lg.size, 0); ImageDraw.Draw(m).rectangle((0, 0, int(lg.width * rev), lg.height), fill=255)
    lgm = lg.copy(); la = np.minimum(np.asarray(lg.split()[3]), np.asarray(m)); lgm.putalpha(Image.fromarray(la))
    paste_center(img, lgm, W / 2, H * (0.58 if not PORTRAIT else 0.55))
    # sheen
    if 690 < f < 760:
        sheen = light_beam(f, 690, 760, 0.5)
        arr = np.asarray(img).astype(float); arr[..., :3] += sheen * np.array(GOLD) * 0.35; img = to_img(arr[..., :3])
    a = ease(seg(f, 725, 750)) * (1 - seg(f, 770, 780))
    overlay_text(img, [('Luxury Meets Technology.', SERIF, 46 if not PORTRAIT else 40, 0.02, GOLD)], H * (0.82 if not PORTRAIT else 0.76), a)
    return img

def scene6(f):  # 26-30s call to action
    img = to_img(BG_IVORY)
    t = seg(f, 780, 900)
    _, _, pim = PROD['TR25005']
    gi = fit(pim, W * (0.7 if PORTRAIT else 0.36), H * 0.3)
    paste_center(img, gi, W / 2, H * (0.25 if not PORTRAIT else 0.24) - 8 * U * ease_io(t), ease(seg(f, 782, 805)))
    lg = fit(LOGO, W * (0.42 if PORTRAIT else 0.17), H * 0.2)
    paste_center(img, lg, W / 2, H * (0.53 if not PORTRAIT else 0.47), ease(seg(f, 790, 815)))
    a = ease(seg(f, 800, 825))
    UR = LANG == 'ur'
    overlay_text(img, [('FIND YOUR PERFECT VISION.', SERIF, 44 if not PORTRAIT else 40, 0.08, (21, 21, 21))], H * ((0.715 if UR else 0.69) if not PORTRAIT else (0.635 if UR else 0.62)), a)
    overlay_text(img, [('OPTICVALLEY.CO', SANS, 40 if not PORTRAIT else 38, 0.32, (143, 106, 47))], H * ((0.81 if UR else 0.78) if not PORTRAIT else (0.715 if UR else 0.7)), ease(seg(f, 812, 835)))
    # SHOP NOW pill
    pa = ease(seg(f, 825, 850))
    if pa > 0:
        layer = Image.new('RGBA', (W, H), (0, 0, 0, 0)); d = ImageDraw.Draw(layer)
        bw, bh = 300 * U, 70 * U; cx, cy = W / 2, H * ((0.9 if LANG == 'ur' else 0.88) if not PORTRAIT else 0.8)
        pulse = 1 + 0.03 * math.sin((f - 825) / FPS * 6) * (f > 850)
        bw *= pulse; bh *= pulse
        d.rounded_rectangle((cx - bw / 2, cy - bh / 2, cx + bw / 2, cy + bh / 2), radius=int(bh / 2), fill=(21, 21, 21, int(255 * pa)))
        if LANG == 'ur':
            ui = urdu_img(URDU['SHOP NOW'], 30 * U, GOLD); layer.alpha_composite(Image.new('RGBA', (1, 1)), (0, 0))
            ui2 = ui.copy(); ui2.putalpha(ui.split()[3].point(lambda v: int(v * pa))); layer.alpha_composite(ui2, (int(cx - ui.width / 2), int(cy - ui.height / 2)))
        else:
            text_tracked(d, (cx, cy), 'SHOP NOW', font(SANS, 24 * U), GOLD + (int(255 * pa),), 0.25)
        img.alpha_composite(layer)
    return img

SCENES = [(0, 150, scene1), (150, 300, scene2), (300, 480, scene3), (480, 630, scene4), (630, 780, scene5), (780, 900, scene6)]
XF = 8  # crossfade frames

def frame(f):
    for i, (a, b, fn) in enumerate(SCENES):
        if a <= f < b:
            img = fn(f)
            if i > 0 and f < a + XF:  # crossfade from previous scene
                prev = SCENES[i - 1][2](a - 1)
                img = Image.blend(prev, img, ease_io((f - a + 1) / XF))
            if f >= N - 12:  # final fade
                img = Image.blend(img, Image.new('RGBA', (W, H), IVORY + (255,)), 0.0)
            return img
    return Image.new('RGBA', (W, H), (0, 0, 0, 255))

if __name__ == '__main__':
    start, end = (int(sys.argv[4]), int(sys.argv[5])) if len(sys.argv) > 5 else (0, N)
    for f in range(start, end):
        p = os.path.join(OUT, f'{f:04d}.jpg')
        if os.path.exists(p): continue
        im = frame(f).convert('RGB')
        g = np.random.default_rng(f).normal(0, 5.5, (H // 2, W // 2, 1)).astype(np.float32)
        g = np.asarray(Image.fromarray(np.clip(g[..., 0] + 128, 0, 255).astype('uint8')).resize((W, H), Image.BILINEAR)).astype(np.float32)[..., None] - 128
        im = Image.fromarray(np.clip(np.asarray(im).astype(np.float32) + g, 0, 255).astype('uint8'))
        im.save(p, quality=93)
        if f % 30 == 0: print(f, flush=True)
