<?php
return [
    'apiKey' => [
        'label' => 'OpenAI API Key',
        'description' => 'Your OpenAI API Key',
        'value' => '',
        'type' => 'string',
    ],
    'model' => [
        'label' => 'OpenAI Model',
        'description' => 'The model to use (e.g. gpt-4)',
        'value' => 'gpt-4',
        'type' => 'string',
    ],
    'maxTokens' => [
        'label' => 'Max Tokens',
        'description' => 'Maximum tokens per request',
        'value' => 2000,
        'type' => 'int',
    ],
    'temperature' => [
        'label' => 'Temperature',
        'description' => 'Controls randomness (0.0 to 1.0)',
        'value' => 0.7,
        'type' => 'float',
    ],
];
