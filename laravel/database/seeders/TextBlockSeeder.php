<?php

namespace Database\Seeders;

use App\Models\TextBlock;
use Illuminate\Database\Seeder;

/** Texts taken verbatim from sstn.sk (audit 2026-11-09) plus the new landing-page copy. */
class TextBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            ['hero.title', 'Úvod – hlavný nadpis', 'text', "Spájame ľudí, ktorí\ntvoria a používajú normy.", 'Dva riadky: prvý tmavomodrý, druhý modrý.'],
            ['hero.lead', 'Úvod – podnadpis', 'text', 'Slovenská spoločnosť pre technickú normalizáciu pripravuje odborné semináre, Stretnutia normalizátorov Slovenska a spoločné podujatia s Českou spoločnosťou pre technickú normalizáciu.', null],
            ['hero.eyebrow', 'Úvod – štítok nad nadpisom', 'text', 'Člen Zväzu slovenských vedeckotechnických spoločností – ZSVTS', null],
            ['events.intro', 'Podujatia – úvod sekcie', 'text', 'Pozvánky a programy si stiahnete priamo pri každom podujatí. Na seminároch sa stretávajú členovia SSTN a ZSVTS, normalizátori z technických komisií aj odborná verejnosť.', null],
            ['mission.intro', 'Poslanie – úvod', 'text', 'SSTN je členom Zväzu slovenských vedeckotechnických spoločností. Spája odborníkov, ktorí technické normy tvoria, aj tých, ktorí ich denne používajú v praxi.', null],
            ['mission.items', 'Poslanie SSTN – body', 'list', implode("\n", [
                'prispievať k rozvoju a uplatňovaniu technickej normalizácie v súlade s potrebami hospodárstva SR;',
                'podporovať komplexný rozvoj a uplatňovanie technickej normalizácie vo všetkých záujmových oblastiach hospodárstva s väzbou na európsku a medzinárodnú normalizáciu;',
                'spolupracovať s orgánmi štátnej správy a inými organizáciami pri používaní a propagácii technickej normalizácie;',
                'prispievať k zvyšovaniu odbornej kvalifikácie v oblasti technickej normalizácie organizovaním školení, kurzov, seminárov a ďalších odborných podujatí (výstavky normalizačnej literatúry a pod.);',
                'poskytovať konzultačnú, poradenskú a informačnú činnosť v oblasti technickej normalizácie;',
                'organizovať výmenu skúseností v oblasti riadenia normalizácie i normalizačnej praxe;',
                'podieľať sa na tvorbe noriem;',
                'publikovať v odborných časopisoch.',
            ]), 'Pôvodný text zo stránky Poslanie SSTN.'],
            ['membership.intro', 'Členstvo – úvod', 'text', 'Členmi spoločnosti môžu byť fyzické a právnické osoby, ktoré vykonávajú činnosti v oblasti technickej normalizácie alebo pri svojej činnosti využívajú slovenské technické normy a súhlasia so stanovami spoločnosti. Členstvo v spoločnosti je individuálne, kolektívne a čestné. Vznik členstva je podmienený podaním prihlášky a zaplatením členského príspevku na príslušný rok.', null],
            ['membership.fees_caption', 'Členstvo – text nad tabuľkou poplatkov', 'text', 'Na XIII. Valnom zhromaždení SSTN bola schválená zmenou stanov SSTN výška členských poplatkov od 1. januára 2012 takto:', null],
            ['membership.vat', 'Členstvo – upozornenie DPH', 'text', 'Slovenská spoločnosť pre technickú normalizáciu nie je platcom DPH!!!', null],
            ['membership.payment', 'Členstvo – platobné pokyny', 'text', 'Upozornenie!!! Od 1. januára 2014 je potrebné úhradu členského vykonať bankovým prevodom alebo vložením na účet SSTN v Slovenskej sporiteľni, účet č. 0011484047/0900.', null],
            ['membership.iban', 'Členstvo – IBAN', 'text', 'SK4209000000000011484047', 'Bez medzier; na webe sa formátuje automaticky.'],
            ['membership.variable_symbol', 'Členstvo – variabilný symbol', 'text', 'Variabilný symbol pre rok 2023: 2023', 'Pôvodný text z webu – rok treba aktualizovať.'],
            ['membership.note', 'Členstvo – poznámka k platbe', 'text', 'Pri platbe uveďte v správe pre prijímateľa meno člena, za ktorého príspevok uhrádzate, aby sme platby mohli identifikovať.', null],
            ['contact.address', 'Kontakt – poštová adresa', 'list', "Slovenská spoločnosť pre technickú normalizáciu\nc/o Zväz slovenských vedeckotechnických spoločností\nKoceľova 15\n815 94 Bratislava", 'Každý riadok adresy na samostatnom riadku.'],
            ['contact.secretary', 'Kontakt – tajomník', 'text', 'Ing. Pavol Radič', null],
            ['contact.email', 'Kontakt – e-mail', 'text', 'radic.sstn@centrum.sk', null],
            ['contact.intro', 'Kontakt – úvodná veta', 'text', 'Napíšte tajomníkovi spoločnosti alebo nám pošlite poštu na adresu Domu ZSVTS.', null],
        ];

        foreach ($blocks as $i => [$key, $label, $type, $value, $hint]) {
            TextBlock::updateOrCreate(['key' => $key], compact('label', 'type', 'value', 'hint') + ['sort' => $i + 1]);
        }
    }
}
