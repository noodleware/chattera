# Chattera

Chattera is a simple chatbot built using Laravel Livewire, Alpine.js, and OpenAI's platform. It allows you to integrate an interactive chatbot into your Laravel application with minimal setup.

## Installation

To install Chattera, use Composer:

```sh
composer require noodleware/chattera
```

Run the migrations to create the necessary database tables:

```sh
php artisan migrate
```

This will create three tables:
- `chats`: Stores session-based chat records.
- `chat_messages`: Holds the conversation exchanges.
- `chat_contexts`: Stores context information that helps the chatbot provide better responses.

Publish the configuration file:

```sh
php artisan vendor:publish --tag=chattera
```

This will allow you to modify the Tailwind CSS classes for styles and adjust the text displayed in the button and header.

## OpenAI API Credentials

To enable Chattera to communicate with OpenAI, you must add your API credentials to the `.env` file:

```ini
CHATTERA_OPEN_AI_TOKEN=your_openai_api_key_here
```

Ensure you replace `your_openai_api_key_here` with your actual OpenAI API key.

## Styling Considerations

The core package CSS will not update automatically. If you change styles in the configuration file, ensure you regenerate the CSS classes in your `app.css` manually.

### Including the Base Stylesheet

After publishing assets, you must include the Chattera base stylesheet in your application:

```blade
<link rel="stylesheet" href="{{ asset('vendor/chattera/chattera.css') }}">
```

## Usage

To include the Chattera chatbot in your application, add the Livewire component, ideally just before the closing `</body>` tag in your template:

```blade
<livewire:chatbot />
```

### Configuring Rules

Review the rules in the configuration file. For example, email addresses used in responses may need to be updated or removed.

### Adding Context

To enhance the chatbot's responses, you can add context using the following Artisan command:

```sh
php artisan chattera:add-context {context}
```

Example:

```sh
php artisan chattera:add-context "To reset your password, you first need to click on the login button in the top right of the menu. Then, you will see the 'Forgot your password' link just below the password field. Click here and follow the on-screen instructions."
```

### Automated Reporting

Chattera includes a command that can be scheduled to send a report daily, weekly, or monthly to the recipient listed in the config file. The command is:

```sh
php artisan chattera:send-report {frequency}
```

For example:

```sh
php artisan chattera:send-report daily
php artisan chattera:send-report weekly
php artisan chattera:send-report monthly
```

Set this command to run at any time after midnight using Laravel's scheduler.

#### Review Process

As part of the reporting process, chat transcripts are sent to OpenAI for review. The review process:
- Assigns a rating out of 100 to the chat session.
- Provides potential improvements to the chatbot's context for bette