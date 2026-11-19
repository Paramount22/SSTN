<?php

use App\Support\RichText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Article bodies move from plain text (paragraphs split by a blank line) to sanitized HTML. */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('articles')->whereNotNull('body')->orderBy('id')->each(function ($article) {
            DB::table('articles')->where('id', $article->id)->update(['body' => RichText::sanitize($article->body)]);
        });
    }

    public function down(): void
    {
        DB::table('articles')->whereNotNull('body')->orderBy('id')->each(function ($article) {
            $paragraphs = preg_split('~</(?:p|h2|h3|li|blockquote)>~i', $article->body);
            $text = collect($paragraphs)->map(fn ($part) => RichText::toPlainText($part))->filter()->implode("\n\n");

            DB::table('articles')->where('id', $article->id)->update(['body' => $text === '' ? null : $text]);
        });
    }
};
