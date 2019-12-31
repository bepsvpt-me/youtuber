<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $channel->name }} | YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdn.datatables.net/w/dt/jq-3.3.1/dt-1.10.18/fh-3.1.4/r-2.2.2/datatables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/w/dt/jq-3.3.1/dt-1.10.18/fh-3.1.4/r-2.2.2/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
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

      .mb-0 {
        margin-bottom: 0;
      }
    </style>
  </head>
  <body>
    <div style="padding: 1rem 1.5rem;">
      <h1 style="margin-bottom: 4px;">{{ $channel->name }}</h1>

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

      <hr style="margin: 1.2rem 0;" />

      <h2 class="mb-0">近期觀看數走勢</h2>

      <div style="height: 300px;">
        <canvas></canvas>
      </div>

      <h2 class="mb-0">數據統計</h2>

      <div style="overflow-x: auto;">
        <table id="overview">
          <thead>
            <th>#</th>
            <th>觀看次數</th>
            <th>留言則數</th>
            <th>喜歡數</th>
            <th>不喜歡數</th>
          </thead>

          <tbody>
            @foreach ([5, 10, 20] as $num)
              @break($num > $videos->count())

              @php($temp = $videos->take($num))

              <tr>
                <td>近 {{ sprintf('%02d', $num) }} 部平均</td>
                <td>{{ number_format($temp->avg('views')) }}</td>
                <td>{{ number_format($temp->avg('comments')) }}</td>
                <td>{{ number_format($temp->avg('likes')) }}</td>
                <td>{{ number_format($temp->avg('dislikes')) }}</td>
              </tr>
            @endforeach

            @foreach (range(1, 3) as $time)
              @php($temp = $videos->where('published_at', '>=', now()->subMonths($time)))

              @continue($temp->isEmpty())

              <tr>
                <td>近 {{ $time }} 個月平均（{{ sprintf('%02d', $temp->count()) }} 部）</td>
                <td>{{ number_format($temp->avg('views')) }}</td>
                <td>{{ number_format($temp->avg('comments')) }}</td>
                <td>{{ number_format($temp->avg('likes')) }}</td>
                <td>{{ number_format($temp->avg('dislikes')) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <h2 class="mb-0">影片列表</h2>

      <div style="overflow-x: auto;">
        <table id="videos">
          <thead>
            <tr>
              <th>#</th>
              <th class="t-left">影片名稱</th>
              <th>觀看次數</th>
              <th>留言則數</th>
              <th>喜歡數</th>
              <th>不喜歡數</th>
              <th>發佈於</th>
              <th>更新於</th>
            </tr>
          </thead>

          <tbody>
            @foreach($videos as $idx => $video)
              <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="t-left"><a href="{{ route('video', ['channel' => $channel->uid, 'video' => $video->uid]) }}">{{ $video->name }}</a></td>
                <td>{{ number_format($video->views) }}</td>
                <td>{{ number_format($video->comments) }}</td>
                <td>{{ number_format($video->likes) }}</td>
                <td>{{ number_format($video->dislikes) }}</td>
                <td title="{{ $video->published_at->setTimezone('Asia/Taipei') }}">{{ $video->published_at->diffForHumans() }}</td>
                <td>{{ $video->updated_at->setTimezone('Asia/Taipei') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <script>
      @php($temp = $videos->take(54)->reverse())

      const formatNum = (val) => val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

      new Chart(document.querySelector('canvas').getContext('2d'), {
        type: 'line',
        data: {
          labels: @json($temp->pluck('name')),
          datasets: [{
            label: '觀看數',
            data: @json($temp->pluck('views')),
            backgroundColor: 'rgba(54, 162, 235, .2)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 2,
            fill: false,
          }],
        },
        options: {
          maintainAspectRatio: false,
          tooltips: {
            mode: 'index',
            intersect: false,
            callbacks: {
              label: (tooltipItem, data) => `${data.datasets[tooltipItem.datasetIndex].label}: ${formatNum(tooltipItem.yLabel)}`,
            }
          },
          scales:{
            xAxes: [{
              display: false,
            }],
            yAxes: [{ ticks: { callback: formatNum } }],
          },
        },
      });

      $('#overview').DataTable({
        info: false,
        ordering: false,
        paging: false,
        searching: false,
      });

      $('#videos').DataTable({
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
