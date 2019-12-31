<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
      html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        height: 100%;
        margin: 0;
      }

      a {
        text-decoration: none;
      }

      .th-info {
        width: 12%;
      }

      .td-info {
        text-align: center;
        white-space: nowrap;
      }
    </style>
  </head>
  <body>
    <div style="padding: 1rem 3rem;">
      <h1 style="margin-bottom: 0;">YouTuber</h1>

      <table style="margin-top: 1rem;">
        <thead>
          <tr>
            <th>#</th>
            <th>頻道名稱</th>
            <th class="th-info">訂閱數</th>
            <th class="th-info">觀看次數</th>
            <th class="th-info">影片數</th>
            <th class="th-info">創立於</th>
            <th class="th-info">更新於</th>
          </tr>
        </thead>

        <tbody>
          @foreach($channels as $idx => $channel)
            <tr>
              <td class="td-info">{{ $idx + 1 }}</td>
              <td><a href="{{ route('channel', ['channel' => $channel->uid]) }}">{{ $channel->name }}</a></td>
              <td class="td-info">{{ number_format($channel->subscribers) }}</td>
              <td class="td-info">{{ number_format($channel->views) }}</td>
              <td class="td-info">{{ number_format($channel->videos) }}</td>
              <td class="td-info">{{ $channel->published_at->setTimezone('Asia/Taipei') }}</td>
              <td class="td-info">{{ $channel->updated_at->setTimezone('Asia/Taipei') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </body>
</html>
