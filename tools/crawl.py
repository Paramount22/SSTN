"""Crawl sstn.sk (HTML pages only), extract content inventory. Files are HEAD-checked, not downloaded."""
import json, re, sys, time, hashlib, os
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse, urldefrag, unquote
from urllib.request import Request, urlopen

OUT = sys.argv[1]
BASE = "https://sstn.sk"
HOSTS = {"sstn.sk", "www.sstn.sk"}
FILE_EXT = re.compile(r"\.(pdf|docx?|xlsx?|pptx?|ppsx?|zip|rar|7z|odt|ods|odp|rtf|txt|csv|mp4|mp3|avi|wmv)$", re.I)
IMG_EXT = re.compile(r"\.(jpe?g|png|gif|webp|svg|bmp)$", re.I)
UA = {"User-Agent": "Mozilla/5.0 (site-inventory)"}

class P(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.links, self.imgs, self.heads, self.meta = [], [], [], {}
        self.title = ""; self._t = None; self._a = None; self._h = None
        self.text = []; self._skip = 0; self.scripts = []; self.styles = []; self.iframes = []
    def handle_starttag(self, tag, a):
        a = dict(a)
        if tag in ("script", "style"):
            self._skip += 1
            if tag == "script" and a.get("src"): self.scripts.append(a["src"])
        if tag == "link" and a.get("rel") == "stylesheet": self.styles.append(a.get("href"))
        if tag == "title": self._t = ""
        if tag == "meta":
            k = a.get("name") or a.get("property")
            if k: self.meta[k] = a.get("content", "")
        if tag == "a" and a.get("href"): self._a = {"href": a["href"], "text": "", "target": a.get("target")}
        if tag == "img": self.imgs.append({"src": a.get("src"), "alt": a.get("alt", None), "w": a.get("width"), "h": a.get("height"), "in_link": self._a["href"] if self._a else None})
        if tag == "iframe": self.iframes.append(a.get("src"))
        if re.fullmatch(r"h[1-6]", tag): self._h = [tag, ""]
    def handle_endtag(self, tag):
        if tag in ("script", "style") and self._skip: self._skip -= 1
        if tag == "title" and self._t is not None: self.title = self._t.strip(); self._t = None
        if tag == "a" and self._a: self._a["text"] = " ".join(self._a["text"].split()); self.links.append(self._a); self._a = None
        if self._h and tag == self._h[0]: self.heads.append([self._h[0], " ".join(self._h[1].split())]); self._h = None
    def handle_data(self, d):
        if self._skip: return
        if self._t is not None: self._t += d
        if self._a: self._a["text"] += d
        if self._h: self._h[1] += d
        self.text.append(d)

def norm(u):
    u = urldefrag(u)[0]
    p = urlparse(u)
    if p.netloc in HOSTS: u = BASE + (p.path or "/") + (("?" + p.query) if p.query else "")
    return u.rstrip("/") if u != BASE + "/" else BASE

def get(u, method="GET"):
    r = urlopen(Request(u, headers=UA, method=method), timeout=30)
    return r.status, r.headers, (r.read() if method == "GET" else b""), r.url

os.makedirs(os.path.join(OUT, "pages"), exist_ok=True)
seeds = [BASE] + re.findall(r"<loc>([^<]+)</loc>", open(os.path.join(OUT, "sitemap.xml"), encoding="utf-8").read())
queue = [norm(s) for s in seeds]; seen = set(queue); pages = {}; files = {}; images = {}; external = {}
while queue:
    u = queue.pop(0)
    try:
        st, h, body, final = get(u)
    except Exception as e:
        pages[u] = {"error": str(e)}; continue
    ct = h.get("Content-Type", "")
    if "html" not in ct:
        files.setdefault(u, {"type": ct, "size": h.get("Content-Length"), "linked_from": []}); continue
    html = body.decode("utf-8", "replace")
    fn = hashlib.md5(u.encode()).hexdigest()[:10] + ".html"
    open(os.path.join(OUT, "pages", fn), "w", encoding="utf-8").write(html)
    p = P(); p.feed(html)
    # main content: strip menu/header/footer by grabbing #pageMainContent-like region if present
    txt = " ".join(" ".join(p.text).split())
    pages[u] = {"file": fn, "status": st, "final": final, "title": p.title, "meta": p.meta, "headings": p.heads,
                "links": [], "images": [], "files": [], "iframes": p.iframes, "scripts": p.scripts, "styles": p.styles,
                "text_len": len(txt)}
    for l in p.links:
        href = l["href"].strip()
        if href.startswith(("mailto:", "tel:", "javascript:")):
            pages[u]["links"].append({"kind": href.split(":")[0], "href": href, "text": l["text"]}); continue
        a = norm(urljoin(u, href)); host = urlparse(a).netloc
        if host and host not in HOSTS and not a.startswith(BASE):
            external.setdefault(a, set()).add(u); pages[u]["links"].append({"kind": "ext", "href": a, "text": l["text"]}); continue
        path = unquote(urlparse(a).path)
        if FILE_EXT.search(path):
            files.setdefault(a, {"linked_from": []})["linked_from"].append(u); pages[u]["files"].append({"href": a, "text": l["text"]}); continue
        if IMG_EXT.search(path):
            images.setdefault(a, {"linked_from": [], "alt": None})["linked_from"].append(u); continue
        pages[u]["links"].append({"kind": "int", "href": a, "text": l["text"]})
        if a not in seen: seen.add(a); queue.append(a)
    for im in p.imgs:
        if not im["src"]: continue
        a = norm(urljoin(u, im["src"]))
        e = images.setdefault(a, {"linked_from": [], "alt": im["alt"]}); e["linked_from"].append(u)
        pages[u]["images"].append({"src": a, "alt": im["alt"], "w": im["w"], "h": im["h"], "in_link": im["in_link"]})
    time.sleep(0.3)

for f, info in list(files.items()) + list(images.items()):
    info["linked_from"] = sorted(set(info["linked_from"]))
    if urlparse(f).netloc in HOSTS or f.startswith(BASE):
        try:
            st, h, _, _ = get(f, "HEAD"); info.update(status=st, type=h.get("Content-Type"), size=h.get("Content-Length"))
        except Exception as e:
            info["status"] = str(e)
json.dump({"pages": pages, "files": files, "images": images, "external": {k: sorted(v) for k, v in external.items()}},
          open(os.path.join(OUT, "inventory.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
print(len(pages), "pages;", len(files), "files;", len(images), "images;", len(external), "external links")
