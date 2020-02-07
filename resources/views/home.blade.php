<!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YouTuber</title>
    <link href="{{ mix('/css/app.css')  }}" rel="stylesheet">
  </head>
  <body>
    <h1>YouTuber</h1>

    <section class="x-scroll mt-1 mb-1">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>頻道名稱</th>
            <th>訂閱數</th>
            <th>觀看次數</th>
            <th>影片數</th>
            <th>創立於</th>
            <th>更新於</th>
          </tr>
        </thead>

        <tbody>
          @foreach($channels as $idx => $channel)
            <tr>
              <td class="t-center">
                @if ($channel->thumbnail)
                  @component('components.image')
                    @slot('alt', $channel->name)
                    @slot('src', route('ggpht', ['payload' => bin2hex(app('aes')->encrypt($channel->thumbnail))]))
                  @endcomponent
                @else
                  <span>{{ $idx + 1 }}</span>
                @endif
              </td>
              <td><a href="{{ route('channel', ['channel' => $channel->uid]) }}">{{ $channel->name }}</a></td>
              <td class="t-right">{{ number_format($channel->subscribers) }}</td>
              <td class="t-right">{{ number_format($channel->views) }}</td>
              <td class="t-right">{{ number_format($channel->videos) }}</td>
              <td class="t-center">{{ $channel->published_at->setTimezone('Asia/Taipei') }}</td>
              <td class="t-center">{{ $channel->updated_at->setTimezone('Asia/Taipei') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </section>
  </body>
</html>
