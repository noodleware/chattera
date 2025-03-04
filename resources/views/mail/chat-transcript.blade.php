<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('chattera.report.branding', 'Chatbot') }} Transcript</title>
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
<h1>Transcript</h1>
<p>
    <strong>#:</strong> {{ $chat->id }} <br>
    <strong>Date:</strong> {{ $chat->created_at->format('m/d/Y H:i:s') }} <br>
    <strong>Name:</strong> {{ $chat->name }} <br>
    <strong>Email:</strong> {{ $chat->email }} <br>
    <strong>Rating:</strong> {{ $chat->rating }} <br>
    <strong>Improvement:</strong> {{ $chat->improvement }} <br>
</p>
<ul style="list-style: none; padding: 0; margin: 0;">
    @foreach($chat->messages as $message)
        <li style="margin-bottom: 10px;">
            <strong>{{ $message->role }}</strong>:
            {{ $message->content }}
        </li>
    @endforeach
</ul>
</body>
</html>
