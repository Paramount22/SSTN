# sstn.sk – audit & redesign plan

Audit date: 2026-11-09 · Source: full crawl of https://sstn.sk (sitemap + every internal link)
Raw data: [audit/](audit/) (`pages.csv`, `files.csv`, `images.csv`, `external-links.csv`, `inventory.json`) · crawler: [tools/crawl.py](tools/crawl.py)

---

## 1. What is there today

| Item | Count | Notes |
|---|---|---|
| Unique HTML pages | **49** | + 3 old path variants that 301 to the canonical URL |
| Downloadable files | **99** (≈ 81 MB) | 80 PDF, 12 DOC, 4 DOCX, 2 RTF, 1 ZIP – all under `/storage/…` |
| Images | **216** (≈ 25 MB) | ≈108 photos + their `_prev.jpg` thumbnails, 4 logos/banners |
| External links | 25 unique | mostly zsvts.sk, unms.sk / normoff.gov.sk, cvtisr.sk |
| YouTube embeds | 3 | in the sidebar of **every** page |

**Platform:** ClickEshop.sk (hosted Slovak CMS/e-shop builder), jQuery + 6 scripts per page.
**DNS:** nameservers `ns1/ns2.clickeshop.com`, MX → `mail.clickeshop.com` (ClickEshop also runs DNS and mail for the domain).

### Site structure today
```
Úvod  ← long feed with ALL events 2016–2026 (~30+ events, 46 attachments)
Poslanie SSTN
  └ Výňatok zo Stanov SSTN
Členstvo
Akcie
  ├ SSTN: 2018 (SDN2018 → Prezentácie, Ceny V. Lista), 2017 (SDN 2017 → Prednášky),
  │       2016 (Stretnutie normalizátorov, Svetový deň normalizácie), Valné zhromaždenie (2011, 2014)
  └ ZSVTS: 2018, 2017 (4 events), 2016 (4 events), 2015
Informácie
  └ VTS News   (+ hidden: Archív → Úvod-2017-02)
Fórum normalizátorov
  ├ Forum (m39), 2017 (+ Galéria: 4 albums), 2016 (Program, Organizačné pokyny), 2014
Kontakty
```
The menu archive stops in **2018**. Everything from 2019 to 2026 exists **only** in the homepage feed and has no URL of its own.

### Problems found (SEO, UX, tech)

**SEO**
- Titles are generic and unbranded (`Úvod`, `2018`, `2014`, `Archiv`) – several pages share the same title.
- 41 of 49 pages have no meta description; the homepage one is just "Normalizácia. SÚTN".
- Only 7 of 49 pages have an `<h1>`; the homepage has none and 80 `<h2>`s.
- 212 of 216 images have no `alt` text.
- Events have no individual pages → Google cannot index or show them as events.
- `robots.txt` points to `http://sstn.sk/sitemap.xml` (should be https); the sitemap is missing `/forum-normalizatorov/forum/m39`.
- Google Analytics is Universal Analytics (`UA-76196225-1`), which stopped collecting data in July 2023 – there is no working analytics.
- Positive: canonical tags exist, http→https and www→non-www 301s work, real 404s are returned.

**Performance**
- 3 YouTube iframes load on every page (the heaviest part of each page view).
- 638 inline `style=""` attributes on the homepage (copied Word/editor formatting, fixed font sizes).
- Full-size gallery JPGs are not optimised (no WebP/AVIF, no responsive sizes).

**Broken / outdated (content owner should confirm)**
- Broken internal file: `/storage/akcie/2016/normalizatori_slovenska/sstn_seminar_december2016.pdf` (404), linked from `/informacie/archiv/uvod-2017-02/m19`.
- Broken external links (404/SSL): 2× old unms.sk SDN 2016 files, 3× worldstandardscooperation.org, 1× iso.org (World Standards Day 2016).
- Many `unms.sk` links now redirect to `normoff.gov.sk`; `http://` links redirect to `https://`.
- Členstvo says "Variabilný symbol pre rok 2023"; the membership form is a 2016 `.doc`.
- Sidebar still promotes the COVID-19 free standards notice.
- Menu typos: "Valné zhromažd**n**ie", "Stretnutie normaliz**a**torov", "Prezentacia_organizacia"; text typo "Exurzia".
- Contact e-mail is plain text, not a `mailto:` link.
- Footer: "vytvorené systémom ClickEshop.sk".

---

## 2. Recommended technology

**Astro (static output) + Markdown content + a Git-based CMS, on static hosting.**

| Layer | Choice | Why |
|---|---|---|
| Site generator | **Astro**, `output: 'static'` | Multi-page site: every URL is a real, fully rendered HTML file. Zero JavaScript by default. Best possible crawlability and Core Web Vitals. Not an SPA. |
| Content | **Astro content collections** (Markdown + typed frontmatter) | Text stays plain and portable; schema validation catches missing dates/titles. |
| Styling | Plain CSS with design tokens (or Tailwind if you prefer it) | Small site, no framework needed. |
| Images | `astro:assets` `<Picture>` | Generates AVIF/WebP, correct `width/height`, lazy loading, from the original JPGs. |
| Small interactivity | Native `<dialog>` lightbox or PhotoSwipe, `lite-youtube-embed`, CSS/vanilla-JS mobile menu | Progressive enhancement – everything works without JS. |
| Editing | **Pages CMS** (hosted, edits the GitHub repo through a `.pages.yml` config). Alternative: Decap/Sveltia CMS | Non-technical editor can add an event + upload PDFs in a form; each save rebuilds the site. No server, no database, nothing to patch. |
| Hosting | **Cloudflare Pages** (free, EU edge, `_redirects`, cookie-less Web Analytics). Netlify is equivalent | Free for this traffic, HTTPS, deploy previews, instant rollbacks. |
| SEO | `@astrojs/sitemap`, JSON-LD (`Organization`, `Event`, `BreadcrumbList`), per-page title/description | |
| Analytics | Cloudflare Web Analytics (cookie-less → no cookie banner) + Google Search Console | Replaces the dead UA tag; GDPR-friendly. |

**Why not the alternatives**
- *Next.js / Nuxt / SvelteKit*: possible in static mode, but ship a JS runtime and hydration for a site that needs none.
- *WordPress (classic theme)*: also Google-friendly and the right choice **only if** the client insists on a WordPress-style admin and will pay for hosting and ongoing updates. For ~5 new events a year it is more maintenance and attack surface than value.
- *Staying on ClickEshop with a new template*: keeps the URL `m5`/`m7` structure, the editor that produced the inline styles, and the analytics/DNS lock-in.

---

## 3. New information architecture

```
/                               Úvod – who SSTN is, next upcoming events, latest news, quick links
/o-nas/                         Poslanie SSTN
/o-nas/stanovy/                 Výňatok zo Stanov SSTN
/clenstvo/                      Členstvo (fees table, payment info, prihláška download)
/podujatia/                     All events, newest first, filter by year and type
/podujatia/2026/<slug>/         One page per event (date, place, organiser, text, attachments, photos)
/dokumenty/                     Auto-generated index of every file (pozvánky, programy, prezentácie)
/galeria/                       Photo albums (Fórum normalizátorov 2017: 4 albums, SDN photos)
/informacie/                    VTS News, Zákon o TN info, useful links, archive
/kontakt/                       Address, secretary, e-mail (mailto)
/storage/...                    ALL files and photos at exactly the same paths as today
```

Event types (for filters and labels): *SSTN · ZSVTS · Fórum normalizátorov · Svetový deň normalizácie · Spoločné s ČSTN · Valné zhromaždenie*.

The ~30 events in the homepage feed (2016–2026) and the ~25 old event pages (2011–2018) all become entries in **one** `events` collection. The homepage then shows only upcoming/latest items, and the archive stays complete and linkable.

### Event content model (frontmatter)
```yaml
title: "Stretnutie normalizátorov Slovenska 2026"
date: 2026-11-20
endDate:            # optional
location: "Dom ZSVTS, Koceľova 15, Bratislava"
organizers: [SSTN, ÚNMS SR]
type: sstn
summary: "…"        # used for meta description + cards
attachments:
  - { label: "Pozvánka a program", file: /storage/akcie/2026/sstn_seminar_november_2026.pdf }
gallery: []         # optional list of photos
legacyUrl: /akcie/sstn/2018/sdn2018/m56   # used to generate the 301 map
```
`attachments` render with type and size badges (e.g. "PDF · 355 kB"), computed at build time.

---

## 4. Keeping the same text, links, files and photos

1. **Files and photos keep their URLs.** Copy all 99 files and 216 images into `public/storage/` (or `src/assets` for photos that get optimised versions) with identical paths. Existing links from Google, e-mails and partner sites keep working. Optimised copies are generated alongside; originals stay downloadable.
2. **Text is migrated by script, not by hand.** Extract each page's `<main>` HTML, strip inline styles/fonts/empty paragraphs, convert to Markdown (e.g. `turndown`), keep every link. The homepage feed is split into individual events semi-manually (headings and attachments give clear boundaries).
3. **Links**: internal links rewritten to new URLs; external links kept as they are, with the dead ones from §1 listed for the client to decide (remove, replace, or keep as plain text).
4. **Page URLs** (decision needed, see §7):
   - *Recommended:* new clean Slovak URLs + a complete **301 map** for all 49 old URLs and the 3 known variants (generated from `legacyUrl`). The old CMS also resolved any path ending in `/mNN`, so add a catch-all redirect rule for that pattern (Cloudflare redirect rule with wildcard) to cover unknown variants used by external sites.
   - *Zero-risk option:* keep the exact old URLs (`/poslanie-sstn/m5` etc.). Simpler, but keeps the `mNN` noise.
5. **Automated proof.** After migration, re-run the crawler against the new site and check:
   - every old URL → 301 → 200 on the right page;
   - every `/storage/...` file and image → 200;
   - per-page word count / text diff old vs new (no lost text);
   - every external link from the old site still present.

---

## 5. Design direction

- Calm, institutional, trustworthy: white space, one accent colour from the logo, strong typographic hierarchy instead of decorative effects.
- Font with full Slovak/Czech diacritics (e.g. Inter or Source Sans 3), **self-hosted** – no Google Fonts CDN (EU GDPR rulings).
- Event cards with a clear date block, place and organiser; attachments as visible buttons, not inline text links.
- Partner strip (ZSVTS, ÚNMS SR / normoff.gov.sk, ČSTN) and the three "Víťazné video" YouTube items moved from the sidebar to one page, using click-to-load thumbnails.
- Mobile first (Google indexes the mobile version); WCAG 2.1 AA contrast, focus states, keyboard navigation.
- Need from client: the logo as vector (SVG/AI/PDF) – today only a 208×189 PNG exists.

---

## 6. Phases

| # | Phase | Output | Rough effort |
|---|---|---|---|
| 0 | **Decisions & access** | Answers to §7, access to domain holder/registrar, ClickEshop contract/notice period | – |
| 1 | **Content export** | Full download of `/storage` + page HTML (crawler already written), cleaned Markdown | 1 day |
| 2 | **IA & design** | Sitemap, wireframes, visual design of 5 templates (home, page, event list, event detail, gallery) | 2–4 days |
| 3 | **Build** | Astro project, components, content collections, CMS config, search-friendly meta, JSON-LD | 4–6 days |
| 4 | **Content migration** | 49 pages + ~30 events split from the homepage, alt texts for photos, link rewrite | 2–3 days |
| 5 | **QA** | Redirect/link/text-diff checks (§4.5), Lighthouse ≥ 95, mobile, accessibility, client review on a preview URL | 1–2 days |
| 6 | **Launch** | Move DNS (keep MX/mail records!), go live, submit sitemap in Search Console, watch 404s/coverage for 4–8 weeks | ½ day + monitoring |

Effort is a rough estimate for one developer; phase 4 depends on how many old texts the client wants corrected.

---

## 7. Open decisions (need answers before phase 2)

1. **Who will update the site, and how often?** (Decides Pages CMS vs. "developer updates Markdown" vs. WordPress.)
2. **Domain and e-mail.** Who is the holder of `sstn.sk` and through which registrar? ClickEshop runs DNS and MX (`mail.clickeshop.com`) – is any `@sstn.sk` mailbox in use? If yes, mail has to be moved (or the MX kept) before switching DNS.
3. **ClickEshop contract** – end date / notice period, and whether they provide an export.
4. **Old page URLs**: clean new URLs + 301s (recommended) or keep the old `…/m5` URLs?
5. **Text corrections**: keep text 100 % identical, or fix the typos and outdated items listed in §1 (VS 2023, COVID notice, dead links, 2016 form)?
6. **Brand assets**: vector logo, preferred colours, any photos newer than 2017.
7. **Language**: Slovak only (as today), or also English?
