@component('mail::message')
# {{ config('chattera.report.branding', 'Chatbot') }} Report

Below is the summary of chats:

<table style="width: 100%; border-collapse: collapse; text-align: left;">
<thead>
<tr>
<th style="border: 1px solid #ddd; padding: 8px;">#</th>
<th style="border: 1px solid #ddd; padding: 8px;">Email</th>
<th style="border: 1px solid #ddd; padding: 8px;">Satisfaction Score</th>
</tr>
</thead>
<tbody>
@foreach ($chats as $chat)
<tr>
<td style="border: 1px solid #ddd; padding: 8px;">{{ $chat->id }}</td>
<td style="border: 1px solid #ddd; padding: 8px;">{{ $chat->email }}</td>
<td style="border: 1px solid #ddd; padding: 8px;">{{ $chat->rating }}</td>
</tr>
@endforeach
</tbody>
</table>

<br />

Each chat transcript is attached as a PDF file.

Thanks,<br>
{{ config('chattera.report.branding', 'Chatbot') }}
@endcomponent
