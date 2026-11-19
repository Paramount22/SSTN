# SSTN – Color Palette

**Slovenská spoločnosť pre technickú normalizáciu**

## Brand colors

| Role      | Name      | HEX       | RGB         | Used for                                |
| --------- | --------- | --------- | ----------- | --------------------------------------- |
| Primary   | SSTN Navy | `#00295C` | 0, 41, 92   | Wordmark "SSTN", company name, headings |
| Secondary | SSTN Blue | `#0080D0` | 0, 128, 208 | Upper frame arc, links, highlights      |
| Accent    | SSTN Red  | `#DF0220` | 223, 2, 32  | Lower frame arc, small accents only     |

## Supporting neutrals

Recommended for documents, web and presentations (not part of the logo itself).

| Role                      | Name       | HEX       | RGB           |
| ------------------------- | ---------- | --------- | ------------- |
| Light background / panels | Light Gray | `#F2F4F7` | 242, 244, 247 |
| Body text                 | Dark Gray  | `#3A4250` | 58, 66, 80    |

## Usage ratio

| Color     | Share |
| --------- | ----- |
| White     | ~60 % |
| SSTN Navy | ~30 % |
| SSTN Blue | ~7 %  |
| SSTN Red  | ~3 %  |

Use red as an accent only. Too much red makes the brand look alarming or political.

## CSS variables

```css
:root {
  --sstn-navy: #00295c;
  --sstn-blue: #0080d0;
  --sstn-red: #df0220;
  --sstn-white: #ffffff;
  --sstn-light-gray: #f2f4f7;
  --sstn-dark-gray: #3a4250;
}
```

## Notes

- The HEX values were sampled from a raster version of the logo. Confirm them when the final vector master is created.
- For print, set official **CMYK** and **Pantone** values together with the printer. Do not convert automatically from HEX, because navy and red shift noticeably in print.
