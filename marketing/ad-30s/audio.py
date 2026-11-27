"""Original 30s soundtrack + voiceover mix for the OpticValley commercial (synthesised, royalty-free)."""
import os, subprocess, wave
import numpy as np

SR = 48000; DUR = 30.0; N = int(SR * DUR)
OUT = os.path.join(os.path.dirname(__file__), 'out')
os.makedirs(OUT, exist_ok=True)
t = np.arange(N) / SR
rng = np.random.default_rng(3)

def env(start, attack, hold, release):
    e = np.zeros(N); s = int(start * SR)
    a, h, r = int(attack * SR), int(hold * SR), int(release * SR)
    seg = np.concatenate([np.linspace(0, 1, max(a, 1)), np.ones(h), np.linspace(1, 0, max(r, 1))])
    e[s:s + len(seg)] = seg[:max(0, N - s)]
    return e

def lowpass(x, cutoff):
    a = np.exp(-2 * np.pi * cutoff / SR); y = np.zeros_like(x); acc = 0.0
    for i in range(0, len(x), 1):
        acc = (1 - a) * x[i] + a * acc; y[i] = acc
    return y

def lp_fast(x, cutoff):  # vectorised one-pole via FFT approximation
    X = np.fft.rfft(x); f = np.fft.rfftfreq(len(x), 1 / SR)
    X *= 1 / np.sqrt(1 + (f / cutoff) ** 4)
    return np.fft.irfft(X, len(x))

def note(freq, start, length, vol=0.2, harmonics=(1, 0.5, 0.25), attack=1.2, release=1.5):
    s = np.zeros(N)
    for k, h in enumerate(harmonics, 1):
        for det in (-0.3, 0.3):
            s += h * np.sin(2 * np.pi * (freq * k + det) * t + k)
    return s * env(start, attack, max(0, length - attack - release), release) * vol

mix = np.zeros(N)

# 0–5s atmospheric pad (Dm9), 5–21s progression, 21–30 reveal (F → C/E → Dm)
chords = [(0, 5, [146.83, 220.0, 261.63, 329.63]), (5, 5.5, [116.54, 174.61, 233.08, 293.66]),
          (10.5, 5.5, [130.81, 196.0, 261.63, 329.63]), (16, 5, [110.0, 164.81, 220.0, 277.18]),
          (21, 5, [174.61, 261.63, 349.23, 440.0]), (26, 4, [146.83, 220.0, 293.66, 349.23])]
for st, ln, fr in chords:
    for f in fr:
        mix += note(f, st, ln + 0.6, 0.035)
mix = lp_fast(mix, 2600)

# deep cinematic bass impacts at scene changes
for st in [0.05, 5.0, 10.0, 16.0, 21.0, 26.0, 29.2]:
    s = int(st * SR); L = int(2.2 * SR); tt = np.arange(L) / SR
    boom = np.sin(2 * np.pi * (55 * np.exp(-tt * 1.4) + 30) * tt) * np.exp(-tt * 2.2)
    boom += 0.3 * rng.standard_normal(L) * np.exp(-tt * 18)
    mix[s:s + L] += boom[:max(0, N - s)] * (0.55 if st not in (21.0, 29.2) else 0.75)

# stylish rhythm 5–21s at 100 bpm (soft kick + shaker), builds in 10–16
bpm = 100; beat = 60 / bpm
for i in range(int((21 - 5) / beat)):
    st = 5 + i * beat; s = int(st * SR)
    L = int(0.35 * SR); tt = np.arange(L) / SR
    kick = np.sin(2 * np.pi * (90 * np.exp(-tt * 25) + 45) * tt) * np.exp(-tt * 9)
    mix[s:s + L] += kick * (0.25 if st < 10 else 0.32)
    hs = int((st + beat / 2) * SR); HL = int(0.08 * SR)
    hat = rng.standard_normal(HL) * np.exp(-np.arange(HL) / SR * 60)
    mix[hs:hs + HL] += np.diff(np.concatenate([[0], hat])) * (0.05 if st < 10 else 0.08)

# arpeggio sparkle 10–16 (sophisticated build)
arp = [523.25, 659.25, 783.99, 987.77]
for i in range(int(6 / (beat / 2))):
    st = 10 + i * beat / 2; f = arp[i % 4]
    s = int(st * SR); L = int(0.5 * SR); tt = np.arange(L) / SR
    mix[s:s + L] += np.sin(2 * np.pi * f * tt) * np.exp(-tt * 7) * 0.04

# futuristic optical shimmer 16–21 + riser into reveal
sh = np.zeros(N); m = (t >= 16) & (t < 21)
sh[m] = np.sin(2 * np.pi * (1200 + 400 * np.sin(2 * np.pi * 0.5 * t[m])) * t[m]) * 0.015
mix += sh
rs = (t >= 18.5) & (t < 21)
noise = rng.standard_normal(N) * rs
mix += lp_fast(noise, 3000) * ((t - 18.5) / 2.5).clip(0, 1) ** 2 * 0.22

# whooshes at transitions
for st in [4.75, 9.75, 15.75, 20.75, 25.75]:
    s = int(st * SR); L = int(0.6 * SR); tt = np.arange(L) / SR
    w = rng.standard_normal(L) * np.sin(np.pi * tt / 0.6) ** 2
    mix[s:s + L] += lp_fast(np.pad(w, (0, 0)), 2500)[:L] * 0.25

# ---- voiceover (macOS TTS, British "Daniel") ----
VO = [(0.9, 'See beyond ordinary.'), (6.0, 'Discover the style that defines you.'),
      (11.2, 'Experience eyewear where precision meets elegance.'), (17.2, 'Your vision. Your style.'),
      (22.6, 'OpticValley. Luxury meets technology.')]
vo = np.zeros(N)
for i, (st, line) in enumerate(VO):
    aiff = os.path.join(OUT, f'vo{i}.aiff'); raw = os.path.join(OUT, f'vo{i}.raw')
    subprocess.run(['say', '-v', 'Daniel', '-r', '150', '-o', aiff, line], check=True)
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-i', aiff, '-f', 's16le', '-ac', '1', '-ar', str(SR), raw], check=True)
    v = np.frombuffer(open(raw, 'rb').read(), dtype=np.int16).astype(float) / 32768
    s = int(st * SR); vo[s:s + len(v)] += v[:max(0, N - s)] * 0.9
    os.remove(raw); os.remove(aiff)

# duck music under voice
venv = np.convolve(np.abs(vo), np.ones(4800) / 4800, mode='same')
duck = 1 - 0.45 * np.clip(venv * 12, 0, 1)
final = mix * duck + vo
final[-int(0.8 * SR):] *= np.linspace(1, 0, int(0.8 * SR))
final = final / max(1e-6, np.abs(final).max()) * 0.89
stereo = np.stack([final, np.roll(final, 24)], axis=1)
with wave.open(os.path.join(OUT, 'soundtrack.wav'), 'wb') as w:
    w.setnchannels(2); w.setsampwidth(2); w.setframerate(SR)
    w.writeframes((stereo * 32767).astype(np.int16).tobytes())
print('soundtrack.wav written')
