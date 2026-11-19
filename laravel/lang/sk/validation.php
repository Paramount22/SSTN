<?php

/*
| Slovak validation messages. Covers the rules this app uses plus the common ones;
| any rule missing here falls back to Laravel's English text (fallback_locale).
| Admin form requests name their fields in Slovak via attributes(); fields without
| such a name (login, profile, password) are named in 'attributes' below.
*/

return [
    'accepted' => 'Pole :attribute musí byť potvrdené.',
    'after' => 'Pole :attribute musí byť neskôr ako :date.',
    'after_or_equal' => 'Pole :attribute nesmie byť skôr ako :date.',
    'alpha_dash' => 'Pole :attribute môže obsahovať len písmená bez diakritiky, číslice, pomlčky a podčiarkovníky.',
    'array' => 'Pole :attribute musí byť zoznam.',
    'before' => 'Pole :attribute musí byť skôr ako :date.',
    'before_or_equal' => 'Pole :attribute nesmie byť neskôr ako :date.',
    'between' => [
        'array' => 'Pole :attribute musí mať :min až :max položiek.',
        'file' => 'Súbor :attribute musí mať :min až :max kB.',
        'numeric' => 'Pole :attribute musí byť medzi :min a :max.',
        'string' => 'Pole :attribute musí mať :min až :max znakov.',
    ],
    'boolean' => 'Pole :attribute musí byť áno alebo nie.',
    'confirmed' => 'Potvrdenie poľa :attribute sa nezhoduje.',
    'current_password' => 'Zadané heslo nie je správne.',
    'date' => 'Pole :attribute musí byť platný dátum.',
    'date_format' => 'Pole :attribute musí mať formát :format.',
    'different' => 'Polia :attribute a :other sa musia líšiť.',
    'digits' => 'Pole :attribute musí mať :digits číslic.',
    'distinct' => 'Pole :attribute obsahuje duplicitnú hodnotu.',
    'email' => 'Pole :attribute musí byť platná e-mailová adresa.',
    'ends_with' => 'Pole :attribute musí končiť jednou z hodnôt: :values.',
    'exists' => 'Zvolená hodnota poľa :attribute neexistuje.',
    'extensions' => 'Súbor :attribute musí mať jednu z prípon: :values.',
    'file' => 'Pole :attribute musí byť súbor.',
    'filled' => 'Pole :attribute musí byť vyplnené.',
    'image' => 'Súbor :attribute musí byť obrázok.',
    'in' => 'Zvolená hodnota poľa :attribute nie je platná.',
    'integer' => 'Pole :attribute musí byť celé číslo.',
    'lowercase' => 'Pole :attribute musí byť napísané malými písmenami.',
    'max' => [
        'array' => 'Pole :attribute môže mať najviac :max položiek.',
        'file' => 'Súbor :attribute môže mať najviac :max kB.',
        'numeric' => 'Pole :attribute môže byť najviac :max.',
        'string' => 'Pole :attribute môže mať najviac :max znakov.',
    ],
    'mimes' => 'Súbor :attribute musí byť typu: :values.',
    'mimetypes' => 'Súbor :attribute musí byť typu: :values.',
    'min' => [
        'array' => 'Pole :attribute musí mať aspoň :min položiek.',
        'file' => 'Súbor :attribute musí mať aspoň :min kB.',
        'numeric' => 'Pole :attribute musí byť aspoň :min.',
        'string' => 'Pole :attribute musí mať aspoň :min znakov.',
    ],
    'not_in' => 'Zvolená hodnota poľa :attribute nie je platná.',
    'numeric' => 'Pole :attribute musí byť číslo.',
    'password' => [
        'letters' => 'Pole :attribute musí obsahovať aspoň jedno písmeno.',
        'mixed' => 'Pole :attribute musí obsahovať aspoň jedno veľké a jedno malé písmeno.',
        'numbers' => 'Pole :attribute musí obsahovať aspoň jednu číslicu.',
        'symbols' => 'Pole :attribute musí obsahovať aspoň jeden špeciálny znak.',
        'uncompromised' => 'Toto heslo sa objavilo v uniknutých databázach hesiel. Zvoľte iné.',
    ],
    'present' => 'Pole :attribute musí byť prítomné.',
    'regex' => 'Pole :attribute má neplatný formát.',
    'required' => 'Pole :attribute je povinné.',
    'required_if' => 'Pole :attribute je povinné, ak :other je :value.',
    'required_with' => 'Pole :attribute je povinné, ak je vyplnené :values.',
    'same' => 'Polia :attribute a :other sa musia zhodovať.',
    'size' => [
        'array' => 'Pole :attribute musí mať :size položiek.',
        'file' => 'Súbor :attribute musí mať :size kB.',
        'numeric' => 'Pole :attribute musí byť :size.',
        'string' => 'Pole :attribute musí mať :size znakov.',
    ],
    'starts_with' => 'Pole :attribute musí začínať jednou z hodnôt: :values.',
    'string' => 'Pole :attribute musí byť text.',
    'unique' => 'Táto hodnota poľa :attribute sa už používa.',
    'uploaded' => 'Súbor :attribute sa nepodarilo nahrať. Môže byť príliš veľký pre server.',
    'url' => 'Pole :attribute musí byť platná webová adresa.',
    'uuid' => 'Pole :attribute musí byť platné UUID.',

    'attributes' => [
        'name' => 'meno',
        'email' => 'e-mail',
        'password' => 'heslo',
        'current_password' => 'súčasné heslo',
        'password_confirmation' => 'potvrdenie hesla',
    ],
];
