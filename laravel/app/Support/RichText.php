<?php

namespace App\Support;

use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Rich text from the admin editor. Stored HTML is always sanitized to the formatting
 * the editor offers, so it can be printed unescaped ({!! !!}) on the public site.
 */
final class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    /** Sanitized HTML, or null when empty. Plain text (no tags) becomes paragraphs. */
    public static function sanitize(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if ($value === strip_tags($value)) {
            return self::fromPlainText($value);
        }

        // Empty paragraphs (e.g. the editor's trailing one after a heading) carry no content.
        $clean = trim(preg_replace('~<p>(?:\s|&nbsp;|<br\s*/?>)*</p>~i', '', self::sanitizer()->sanitize($value)));

        // An empty editor sends "<p></p>".
        return trim(html_entity_decode(strip_tags($clean))) === '' ? null : $clean;
    }

    /** Paragraphs separated by a blank line; single line breaks are kept. */
    public static function fromPlainText(string $text): string
    {
        return collect(preg_split('/\R\s*\R/', trim($text)))
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph) => '<p>'.nl2br(e($paragraph), false).'</p>')
            ->implode('');
    }

    /** Text without markup, e.g. for excerpts and meta descriptions. */
    public static function toPlainText(?string $html): string
    {
        $text = preg_replace('~<(?:br\s*/?|/(?:p|h[1-6]|li|blockquote))>~i', ' ', (string) $html);

        return Str::squish(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5));
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('u')
                ->allowElement('s')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('blockquote')
                ->allowElement('a', ['href'])
                // Unknown wrappers lose the tag but keep their text (other elements are dropped whole).
                ->blockElement('div')
                ->blockElement('span')
                ->blockElement('b')
                ->blockElement('i')
                ->blockElement('h1')
                ->blockElement('h4')
                ->blockElement('h5')
                ->blockElement('h6')
                ->allowLinkSchemes(['http', 'https', 'mailto'])
                ->allowRelativeLinks()
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
        );
    }
}
