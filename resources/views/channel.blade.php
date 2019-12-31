<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $channel->name }} | YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdn.datatables.net/w/dt/jq-3.3.1/dt-1.10.18/fh-3.1.4/r-2.2.2/datatables.min.css" rel="stylesheet" />
    <script src="https://cdn.datatables.net/w/dt/jq-3.3.1/dt-1.10.18/fh-3.1.4/r-2.2.2/datatables.min.js"></script>
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

      <a href="{{ route('home') }}">回列表</a>
      <span style="margin: 0 4px;">•</span>
      <span>訂閱數：{{ number_format($channel->subscribers) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>觀看數：{{ number_format($channel->views) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>影片數：{{ number_format($channel->videos) }}</span>
      <span style="margin: 0 4px;">•</span>
      <span>創立於：{{ $channel->published_at->setTimezone('Asia/Taipei') }}</span>
      <span style="margin: 0 4px;">•</span>
      <a
        href="https://www.youtube.com/channel/{{ $channel->uid }}"
        target="_blank"
        rel="noopener noreferrer"
      >
        <span>YouTube</span>

        <svg style="fill: currentColor; width: 14px; height: 14px;" viewBox="0 0 24 24">
          <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z" />
        </svg>
      </a>

      <hr style="margin: 1rem 0;" />

      <table>
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
              <td><a href="{{ route('video', ['channel' => $channel->uid, 'video' => $video->uid]) }}">{{ $video->name }}</a></td>
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

    <script>
      $('table').DataTable({
        columns: [
          null,
          { orderable: false },
          null,
          null,
          null,
          null,
          { orderable: false },
          { orderable: false },
        ],
        fixedHeader: true,
        info: false,
        order: [],
        paging: false,
      });
    </script>
  </body>
</html>
