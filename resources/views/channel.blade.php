<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $channel->name }} | YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
      html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        height: 100%;
        margin: 0;
      }

      .t-center {
        padding: 0 2px;
        text-align: center;
        white-space: nowrap;
      }
    </style>
  </head>
  <body>
    <div style="padding: 1rem 3rem;">
      <h1 style="margin-bottom: 0;">{{ $channel->name }}</h1>

      <a href="{{ route('home') }}" style="text-decoration: none;">回列表</a>
      <span style="margin: 0 4px;">•</span>
      <span>訂閱數：{{ number_format($channel->subscribers) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>觀看數：{{ number_format($channel->views) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>影片數：{{ number_format($channel->videos) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>創立於：{{ $channel->published_at->setTimezone('Asia/Taipei') }}</span>

      <table style="margin-top: 1rem;">
        <thead>
          <tr>
            <th class="t-center">#</th>
            <th>影片名稱</th>
            <th class="t-center">觀看次數</th>
            <th class="t-center">留言則數</th>
            <th class="t-center">喜歡數</th>
            <th class="t-center">不喜歡數</th>
            <th class="t-center">發佈於</th>
            <th class="t-center">更新於</th>
          </tr>
        </thead>

        <tbody>
          @foreach($videos as $idx => $video)
            <tr>
              <td class="t-center">{{ $idx + 1 }}</td>
              <td>
                <a
                  href="{{ route('video', ['channel' => $channel->uid, 'video' => $video->uid]) }}"
                  style="text-decoration: none;"
                >{{ $video->name }}</a>
              </td>
              <td class="t-center">{{ number_format($video->views) }}</td>
              <td class="t-center">{{ number_format($video->comments) }}</td>
              <td class="t-center">{{ number_format($video->likes) }}</td>
              <td class="t-center">{{ number_format($video->dislikes) }}</td>
              <td class="t-center" title="{{ $video->published_at->setTimezone('Asia/Taipei') }}">{{ $video->published_at->diffForHumans() }}</td>
              <td class="t-center">{{ $video->updated_at->setTimezone('Asia/Taipei') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </body>
</html>
