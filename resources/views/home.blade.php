<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YouTuber</title>
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

      table {
        text-align: center;
        white-space: nowrap;
      }

      table.dataTable.no-footer {
        border-bottom: 0;
      }

      .t-left {
        text-align: left;
        white-space: initial;
      }
    </style>
  </head>
  <body>
    <div style="padding: 1rem 1.5rem;">
      <h1 style="margin-bottom: 0;">YouTuber</h1>

      <div style="margin-top: 1rem; overflow-x: auto;">
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
                <td>
                  @if ($channel->thumbnail)
                    <img
                      alt="{{ $channel->name }}"
                      height="35"
                      src="{{ $channel->thumbnail }}"
                      style="vertical-align: bottom;"
                      width="35"
                    >
                  @else
                    <span>{{ $idx + 1 }}</span>
                  @endif
                </td>
                <td class="t-left"><a href="{{ route('channel', ['channel' => $channel->uid]) }}">{{ $channel->name }}</a></td>
                <td>{{ number_format($channel->subscribers) }}</td>
                <td>{{ number_format($channel->views) }}</td>
                <td>{{ number_format($channel->videos) }}</td>
                <td>{{ $channel->published_at->setTimezone('Asia/Taipei') }}</td>
                <td>{{ $channel->updated_at->setTimezone('Asia/Taipei') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <script>
      $('table').DataTable({
        columns: [
          { orderable: false },
          null,
          null,
          null,
          null,
          null,
          { orderable: false },
        ],
        info: false,
        order: [],
        paging: false,
      });
    </script>
  </body>
</html>
