<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatbot Rules
    |--------------------------------------------------------------------------
    |
    | These rules define the chatbot's behavior. Developers can customize them
    | by publishing the config file and modifying the rules.
    |
    */
    'rules' => [
        'The assistant will rely on in-app context to answer questions related to app features, usage, and troubleshooting. If there is insufficient context, it will politely inform the user and escalate as needed.',
        'This chatbot is 100% AI-driven and there are no human operators available. For more complex inquiries or unresolved issues, users can email support@example.com and expect a response within 1 working day.',
        'If the assistant cannot provide a confident or complete answer, it will respond with: "Apologies, I don’t have the answer at the moment. For further assistance, please email support@example.com. We typically respond within 1 working day."',
        'The assistant will provide brief, concise responses while ensuring the message is clear and complete. It will maintain a friendly and polite tone throughout.',
        'The assistant will simplify complex explanations or steps to ensure they are easy to understand, avoiding unnecessary technical jargon.',
        'If the user’s intent is unclear, the assistant will politely ask for more details or clarification to better assist them.',
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Define the models and settings used for interacting with the OpenAI API.
    |
    */
    'openai' => [
        'base_url' => 'https://api.openai.com/v1/',
        'access_key' => env('CHATTERA_OPEN_AI_TOKEN', ''),
        'chat_model' => 'gpt-4o-mini',
        'embedding_model' => 'text-embedding-3-small',
        'temperature' => 0.7,
        'frequency_penalty' => 0.5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Limit
    |--------------------------------------------------------------------------
    |
    | This limits the number of tokens for each request to the OpenAI platform.
    | A higher number allows more context to be passed with each new question.
    |
    */
    'token_limit' => 5000,

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Customize colors, texts, and other UI settings for the chatbot.
    |
    */
    'ui' => [
        'button' => [
            'background' => 'nw-bg-indigo-600',
            'hover' => 'nw-bg-indigo-500',
            'text_color' => 'nw-text-white',
            'label' => 'Help',
        ],
        'header' => [
            'gradient' => [
                'from' => 'nw-from-indigo-600',
                'to' => 'nw-to-indigo-900',
            ],
            'text_color' => 'nw-text-white',
            'title' => 'Need help?',
            'subtitle' => 'Get quick answers with our <strong>AI assistant!</strong>',
            'start_chat_label' => 'Start Chat!',
        ],
        'messages' => [
            'greeting' => 'Hello! How can I assist you today?',
            'moderation_warning' => 'This message does not comply with the usage policy.',
        ],
        'links' => [
            'support' => [
                'show' => true,
                'url' => 'https://support.example.com',
                'text' => 'Visit our [Support] center for step-by-step guides and resources.',
            ],
            'terms' => [
                'show' => true,
                'url' => 'https://terms-and-conditions.example.com',
                'text' => 'See our chatbot usage [terms and conditions].',
            ],
        ],
    ],

    'requires_email' => true,

    /*
    |--------------------------------------------------------------------------
    | Report
    |--------------------------------------------------------------------------
    |
    | Customize the list of recipients for the chat report. The report does not
    | send by default you need to add the command to your scheduler.
    | chattera:send-report {frequency: daily|weekly|monthly}
    |
    */

    'report' => [
        'branding' => 'Chatbot',
        'recipients' => [
            'email@example.com',
        ],
    ],
];
