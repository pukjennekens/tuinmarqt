<?php
return [
    'global_settings' => [
        'troublefree_api_company' => [
            'name'        => 'TroubleFree API Bedrijf',
            'description' => 'De bedrijfsnaam die je gebruikt voor in te loggen op TroubleFree.',
            'type'        => 'text',
        ],
        'troublefree_api_username' => [
            'name'        => 'TroubleFree API Gebruikersnaam',
            'description' => 'De gebruikersnaam die je gebruikt voor in te loggen op TroubleFree.',
            'type'        => 'text',
        ],
        'troublefree_api_password' => [
            'name'        => 'TroubleFree API Wachtwoord',
            'description' => 'Het wachtwoord dat je gebruikt voor in te loggen op TroubleFree.',
            'type'        => 'password',
        ],
        'woocommerce_website_url' => [
            'name'        => 'WooCommerce Website URL',
            'description' => 'De URL van de WooCommerce website waarop de artikelen geïmporteerd moeten worden.',
            'type'        => 'text',
        ],
        'woocommerce_consumer_key' => [
            'name'        => 'WooCommerce Consumer Key',
            'description' => 'De consumer key die je kan verkrijgen via de WooCommerce website (WooCommerce > Instellingen > Geavanceerd > REST API > Nieuwe sleutel).',
            'type'        => 'text',
        ],
        'woocommerce_consumer_secret' => [
            'name'        => 'WooCommerce Consumer Secret',
            'description' => 'Het klantgeheim die je kan verkrijgen via de WooCommerce website (WooCommerce > Instellingen > Geavanceerd > REST API > Nieuwe sleutel).',
            'type'        => 'password',
        ],
    ],

    'troublefree_custom_fields' => [
        [
            'name' => 'Merk',
            'key'  => 'brand',
        ],
        [
            'name' => 'Kleur',
            'key'  => 'color',
        ],
        [
            'name' => 'Materiaal',
            'key'  => 'material',
        ],
        [
            'name' => 'Afmetingen',
            'key'  => 'dimensions',
        ],
        [
            'name' => 'Stroefheid',
            'key'  => 'grip',
        ],
        [
            'name' => 'Geschikt voor',
            'key'  => 'suitable_for',
        ],
    ],
];