#!/usr/bin/env python3
"""Webfonts auf Latein reduzieren (Seitengewicht, NF-6).

Originale liegen in assets-src/fonts/ (werden nicht ausgeliefert), die
reduzierten Dateien landen unter gleichem Namen in public/assets/fonts/.
Behalten: Basis-Latein, Latin-1 (Umlaute, ß), Œ/œ, typografische Zeichen, €, ™, Pfeile.
Entfernt: Kyrillisch, Griechisch, Vietnamesisch und Latin Extended-A/B. Einzelne
Zeichen wie š oder ń (im Inhalt nur 2×) fallen auf die Systemschrift zurück;
Latin Extended-A würde ~36 KB mehr kosten.
Alle Achsen (wght, opsz) und OpenType-Features bleiben vollständig erhalten.

Voraussetzung: pip install fonttools brotli
Aufruf:        python3 scripts/subset-fonts.py
"""
from pathlib import Path

from fontTools import subset

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / 'assets-src' / 'fonts'
OUT = ROOT / 'public' / 'assets' / 'fonts'
FONTS = ['CaseVAR.woff2', 'McQueenVAR.woff2', 'SupermarkerVAR-Italic.woff2']
UNICODES = (
    'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,'
    'U+2000-206F,U+20AC,U+2122,U+2190-2199,U+2212,U+2215,U+FEFF,U+FFFD'
)

for name in FONTS:
    src, out = SRC / name, OUT / name
    subset.main([
        str(src),
        f'--unicodes={UNICODES}',
        '--layout-features=*',
        '--flavor=woff2',
        f'--output-file={out}',
    ])
    print(f'{name}: {src.stat().st_size // 1024} KB → {out.stat().st_size // 1024} KB')
