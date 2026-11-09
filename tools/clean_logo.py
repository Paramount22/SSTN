"""Remove the baked-in transparency checkerboard from SSTN_logo/logo_1.png and export web logos.

The source is RGB (no alpha); its background is a white/light-gray checker pattern.
Logo colours (navy, blue, red) are dark or saturated, so alpha is derived from
"ink" = 255 - min(R,G,B) and the colour is un-blended against white.
"""
import sys
from PIL import Image

SRC, OUT = sys.argv[1], sys.argv[2]
LOW, HIGH = 80, 200            # background "ink" reaches 75 (checker gray); above HIGH -> fully opaque

im = Image.open(SRC).convert("RGB")
w, h = im.size
src = im.load()
out = Image.new("RGBA", (w, h))
dst = out.load()
for y in range(h):
    for x in range(w):
        r, g, b = src[x, y]
        ink = 255 - min(r, g, b)
        a = 0.0 if ink <= LOW or max(r, g, b) - min(r, g, b) < 14 else 1.0 if ink >= HIGH else (ink - LOW) / (HIGH - LOW)
        if a == 0:
            dst[x, y] = (0, 0, 0, 0)
            continue
        un = lambda c: max(0, min(255, round(255 - (255 - c) / a)))
        dst[x, y] = (un(r), un(g), un(b), round(a * 255))

full = out.crop(out.getbbox())
fw, fh = full.size
print("full lockup bbox", out.getbbox(), full.size)

# mark only (SSTN + arcs): everything above the subtitle; find the empty row band between them
alpha = full.getchannel("A")
rows = [any(alpha.getpixel((x, y)) > 40 for x in range(0, fw, 2)) for y in range(fh)]
gap_start = None
for y in range(int(fh * 0.55), fh):
    if not rows[y]:
        gap_start = y
        break
mark = full.crop((0, 0, fw, gap_start))
mark = mark.crop(mark.getbbox())
print("mark", mark.size)

def save(img, name, width):
    img = img.resize((width, round(img.height * width / img.width)), Image.LANCZOS)
    img.save(f"{OUT}/{name}.png", optimize=True)
    img.save(f"{OUT}/{name}.webp", quality=92, method=6)
    print(name, img.size)

save(full, "sstn-logo", 900)
save(mark, "sstn-logo-mark", 480)
