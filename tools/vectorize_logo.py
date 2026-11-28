"""Trace SSTN_logo/logo_1.png into vector logos (SVG, PDF, PNG).

    python tools/vectorize_logo.py SSTN_logo/logo_1.png SSTN_logo/export

The source is RGB with a transparency checkerboard baked in (see clean_logo.py).
Each pixel gets an alpha from its "ink" and is assigned to the nearest brand
colour (navy, blue, red). Every colour layer is traced with marching squares at
sub-pixel precision, simplified and smoothed into cubic Bezier curves.

Outputs: sstn-logo-full (mark + name, as in the site footer) and
sstn-logo-mark (SSTN with the arcs, as in the header), each as .svg, .pdf and .png.
Pure Python + Pillow + PyMuPDF; no numpy needed.
"""
import math
import sys
from pathlib import Path

import fitz  # PyMuPDF
from PIL import Image, ImageFilter

SRC, OUT = Path(sys.argv[1]), Path(sys.argv[2])
OUT.mkdir(parents=True, exist_ok=True)

# Brand palette (SSTN_color_palette.md)
COLOURS = {"navy": (0x00, 0x29, 0x5C), "blue": (0x00, 0x80, 0xD0), "red": (0xDF, 0x02, 0x20)}
LOW, HIGH = 80, 200          # same ink thresholds as clean_logo.py
PNG_WIDTH = 2400
TITLE = "SSTN – Slovenská spoločnosť pre technickú normalizáciu"


# --- 1. alpha + colour layers -------------------------------------------------
im = Image.open(SRC).convert("RGB")
W, H = im.size
px = im.load()
layers = {name: Image.new("L", (W, H), 0) for name in COLOURS}
lp = {name: layers[name].load() for name in COLOURS}

for y in range(H):
    for x in range(W):
        r, g, b = px[x, y]
        ink = 255 - min(r, g, b)
        if ink <= LOW or max(r, g, b) - min(r, g, b) < 14:
            continue
        a = 1.0 if ink >= HIGH else (ink - LOW) / (HIGH - LOW)
        un = [255 - (255 - c) / a for c in (r, g, b)]
        name = min(COLOURS, key=lambda n: sum((u - v) ** 2 for u, v in zip(un, COLOURS[n])))
        lp[name][x, y] = round(a * 255)

# A light blur evens out raster noise before tracing.
layers = {n: l.filter(ImageFilter.GaussianBlur(0.9)) for n, l in layers.items()}


# --- 2. marching squares --------------------------------------------------------
def trace(layer, level=128):
    """Closed contours of `layer` at `level`, as lists of (x, y) points."""
    w, h = layer.size
    v = layer.load()
    box = layer.point(lambda p: 255 if p >= level else 0).getbbox()
    if not box:
        return []
    x0, y0, x1, y1 = max(box[0] - 2, 0), max(box[1] - 2, 0), min(box[2] + 2, w - 1), min(box[3] + 2, h - 1)

    def val(x, y):
        return v[x, y] if 0 <= x < w and 0 <= y < h else 0

    def point(edge):
        kind, x, y = edge
        if kind == "h":   # between (x,y) and (x+1,y)
            a, b = val(x, y), val(x + 1, y)
            t = (level - a) / (b - a) if b != a else 0.5
            return (x + t, y)
        a, b = val(x, y), val(x, y + 1)   # "v": between (x,y) and (x,y+1)
        t = (level - a) / (b - a) if b != a else 0.5
        return (x, y + t)

    nxt = {}   # directed: edge -> next edge, with the filled side kept on one side
    for y in range(y0 - 1, y1 + 1):
        for x in range(x0 - 1, x1 + 1):
            tl, tr, br, bl = val(x, y) >= level, val(x + 1, y) >= level, val(x + 1, y + 1) >= level, val(x, y + 1) >= level
            case = tl << 3 | tr << 2 | br << 1 | bl
            if case in (0, 15):
                continue
            T, R, B, L = ("h", x, y), ("v", x + 1, y), ("h", x, y + 1), ("v", x, y)
            segs = {
                1: [(L, B)], 2: [(B, R)], 3: [(L, R)], 4: [(R, T)], 6: [(B, T)], 7: [(L, T)],
                8: [(T, L)], 9: [(T, B)], 11: [(T, R)], 12: [(R, L)], 13: [(R, B)], 14: [(B, L)],
            }.get(case)
            if segs is None:   # saddles 5 and 10, decided by the cell centre
                centre = (val(x, y) + val(x + 1, y) + val(x + 1, y + 1) + val(x, y + 1)) / 4 >= level
                if case == 5:
                    segs = [(L, T), (R, B)] if centre else [(L, B), (R, T)]
                else:
                    segs = [(T, R), (B, L)] if centre else [(T, L), (B, R)]
            for a, b in segs:
                nxt[a] = b

    loops = []
    while nxt:
        start, edge = next(iter(nxt.items()))
        loop = [point(start)]
        cur = start
        while True:
            n = nxt.pop(cur, None)
            if n is None or n == start:
                break
            loop.append(point(n))
            cur = n
        if len(loop) > 8:
            loops.append(loop)
    return loops


# --- 3. simplify + smooth --------------------------------------------------------
def rdp(points, eps):
    if len(points) < 3:
        return points
    (ax, ay), (bx, by) = points[0], points[-1]
    dx, dy = bx - ax, by - ay
    norm = math.hypot(dx, dy) or 1e-9
    idx, dmax = 0, -1.0
    for i in range(1, len(points) - 1):
        px_, py_ = points[i]
        d = abs(dy * px_ - dx * py_ + bx * ay - by * ax) / norm
        if d > dmax:
            idx, dmax = i, d
    if dmax <= eps:
        return [points[0], points[-1]]
    return rdp(points[: idx + 1], eps)[:-1] + rdp(points[idx:], eps)


def simplify_closed(loop, eps=0.8):
    # split at the point farthest from the first one so RDP sees two open halves
    far = max(range(len(loop)), key=lambda i: (loop[i][0] - loop[0][0]) ** 2 + (loop[i][1] - loop[0][1]) ** 2)
    a = rdp(loop[: far + 1], eps)
    b = rdp(loop[far:] + [loop[0]], eps)
    return a[:-1] + b[:-1]


def turn(p0, p1, p2):
    a1 = math.atan2(p1[1] - p0[1], p1[0] - p0[0])
    a2 = math.atan2(p2[1] - p1[1], p2[0] - p1[0])
    return abs((a2 - a1 + math.pi) % (2 * math.pi) - math.pi)


def tangent(a, b, length):
    """Vector along a->b scaled to `length`."""
    dx, dy = b[0] - a[0], b[1] - a[1]
    norm = math.hypot(dx, dy) or 1e-9
    return dx / norm * length, dy / norm * length


def to_path(pts, ox, oy, corner=math.radians(40)):
    """Smooth cubic Beziers through the points; sharp turns stay corners.

    Tangents follow the neighbours' direction (Catmull-Rom) but their length is a
    third of the segment itself, so short segments next to corners cannot overshoot.
    """
    n = len(pts)
    smooth = [turn(pts[i - 1], pts[i], pts[(i + 1) % n]) < corner for i in range(n)]
    f = lambda v: f"{v:.2f}".rstrip("0").rstrip(".")
    P = lambda p: f"{f(p[0] - ox)} {f(p[1] - oy)}"
    d = [f"M{P(pts[0])}"]
    for i in range(n):
        p0, p1, p2, p3 = pts[i - 1], pts[i], pts[(i + 1) % n], pts[(i + 2) % n]
        if not smooth[i] and not smooth[(i + 1) % n]:
            d.append(f"L{P(p2)}")
            continue
        seg = math.hypot(p2[0] - p1[0], p2[1] - p1[1]) / 3
        if smooth[i]:
            tx, ty = tangent(p0, p2, seg)
            c1 = (p1[0] + tx, p1[1] + ty)
        else:
            c1 = p1
        if smooth[(i + 1) % n]:
            tx, ty = tangent(p1, p3, seg)
            c2 = (p2[0] - tx, p2[1] - ty)
        else:
            c2 = p2
        d.append(f"C{P(c1)} {P(c2)} {P(p2)}")
    return "".join(d) + "Z"


shapes = []   # (colour, simplified points)
for name, layer in layers.items():
    for loop in trace(layer):
        pts = simplify_closed(loop)
        if len(pts) >= 3:
            shapes.append((name, pts))

# --- 4. split the mark (SSTN + arcs) from the name below it ----------------------
ys = [p[1] for _, pts in shapes for p in pts]
top, bottom = min(ys), max(ys)
# the name starts after the first empty band of navy rows below the SSTN letters
# (the first one, not a later one: the name itself has two lines)
covered = set()
for c, pts in shapes:
    if c == "navy":
        covered.update(range(math.floor(min(p[1] for p in pts)), math.ceil(max(p[1] for p in pts)) + 1))
mid = top + (bottom - top) * 0.55
gap = min((y for y in range(int(mid), int(bottom)) if y not in covered and y - 1 in covered), default=None)
split = min(y for y in range(gap, int(bottom)) if y in covered) if gap else bottom + 1
print("mark/name split at y =", split)


def bbox(sel):
    xs = [p[0] for _, pts in sel for p in pts]
    ys = [p[1] for _, pts in sel for p in pts]
    return min(xs), min(ys), max(xs), max(ys)


def write(name, sel, pad=4):
    x0, y0, x1, y1 = bbox(sel)
    x0, y0, x1, y1 = x0 - pad, y0 - pad, x1 + pad, y1 + pad
    w, h = x1 - x0, y1 - y0
    paths = []
    for colour in COLOURS:
        d = "".join(to_path(pts, x0, y0) for c, pts in sel if c == colour)
        if d:
            hexc = "#%02X%02X%02X" % COLOURS[colour]
            paths.append(f'  <path fill="{hexc}" fill-rule="evenodd" d="{d}"/>')
    svg = (
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w:.0f} {h:.0f}" width="{w:.0f}" height="{h:.0f}" '
        f'role="img" aria-label="{TITLE}">\n  <title>{TITLE}</title>\n' + "\n".join(paths) + "\n</svg>\n"
    )
    svg_path = OUT / f"{name}.svg"
    svg_path.write_text(svg, encoding="utf-8")

    doc = fitz.open(svg_path)
    pdf = fitz.open("pdf", doc.convert_to_pdf())
    pdf.set_metadata({"title": TITLE, "author": "SSTN"})
    pdf.save(OUT / f"{name}.pdf", garbage=4, deflate=True)
    scale = PNG_WIDTH / doc[0].rect.width
    doc[0].get_pixmap(matrix=fitz.Matrix(scale, scale), alpha=True).save(OUT / f"{name}.png")
    print(name, f"{w:.0f}x{h:.0f}", "paths:", sum(1 for c, _ in sel), "svg bytes:", len(svg.encode()))


write("sstn-logo-full", shapes)
write("sstn-logo-mark", [s for s in shapes if max(p[1] for p in s[1]) < split])
