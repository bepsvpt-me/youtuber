<!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $channel->name }} | YouTuber</title>
    <link href="{{ asset('/css/app.css')  }}" rel="stylesheet">
    <link href="{{ asset('/css/chart.min.css')  }}" rel="stylesheet">
    <script src="{{ asset('/js/chart.min.js') }}"></script>
  </head>
  <body>
    <h1 style="display: flex; align-items: center;">
      @if ($channel->thumbnail)
        @component('components.image')
          @slot('alt', $channel->name)
          @slot('src', route('ggpht', ['payload' => bin2hex(app('aes')->encrypt($channel->thumbnail))]))
          @slot('style', 'margin-right: 6px;')
        @endcomponent
      @endif

      <span>{{ $channel->name }}</span>
    </h1>

    <a href="{{ route('home') }}">回列表</a>
    @include('components.dot')
    <span>訂閱數：{{ number_format($channel->subscribers) }}</span>
    @include('components.dot')
    <span>觀看數：{{ number_format($channel->views) }}</span>
    @include('components.dot')
    <span>影片數：{{ number_format($channel->videos) }}</span>
    @include('components.dot')
    <span>創立於：{{ $channel->published_at->setTimezone('Asia/Taipei') }}</span>
    @include('components.dot')
    @component('components.external-link')
      @slot('href', sprintf('https://www.youtube.com/channel/%s', $channel->uid))

      YouTube
    @endcomponent

    <hr class="mt-1 mb-1" />

    <h2>影片觀看數走勢</h2>

    <div class="mt-1" style="height: 300px;">
      <canvas></canvas>
    </div>

    <h2 class="mt-1">數據統計</h2>

    <div class="x-scroll">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>觀看次數</th>
            <th>留言則數</th>
            <th>喜歡數</th>
            <th>不喜歡數</th>
          </tr>
        </thead>

        <tbody>
          @foreach ([5, 10, 20] as $num)
            @break($num > $videos->count())

            @php($temp = $videos->where('hidden', false)->take($num))

            <tr>
              <td class="t-center">近 {{ sprintf('%02d', $num) }} 部平均</td>
              <td class="t-right">{{ number_format($temp->avg('views')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('comments')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('likes')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('dislikes')) }}</td>
            </tr>
          @endforeach

          @foreach (range(1, 3) as $time)
            @php($temp = $videos->where('hidden', false)->where('published_at', '>=', now()->subMonths($time)))

            @continue($temp->isEmpty())

            <tr>
              <td class="t-center">近 {{ $time }} 個月平均（{{ sprintf('%02d', $temp->count()) }} 部）</td>
              <td class="t-right">{{ number_format($temp->avg('views')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('comments')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('likes')) }}</td>
              <td class="t-right">{{ number_format($temp->avg('dislikes')) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <h2 class="mt-1">影片列表</h2>

    <div class="x-scroll mb-1">
      <table>
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
              <td class="t-center">
                @component('components.image')
                  @slot('alt', $idx + 1)
                  @slot('height', 60)
                  @slot('src', route('ytimg', ['payload' => bin2hex(app('aes')->encrypt($video->uid))]))
                  @slot('width', 80)
                @endcomponent
              </td>
              <td class="video-name">
                <a href="{{ route('video', ['channel' => $channel->uid, 'video' => $video->uid]) }}">{{ $video->name }}</a>
              </td>
              <td class="t-right">{{ number_format($video->views) }}</td>
              <td class="t-right">{{ number_format($video->comments) }}</td>
              <td class="t-right">{{ number_format($video->likes) }}</td>
              <td class="t-right">{{ number_format($video->dislikes) }}</td>
              <td class="t-right" title="{{ $video->published_at->setTimezone('Asia/Taipei') }}">{{ $video->published_at->diffForHumans() }}</td>
              <td class="t-right" title="{{ $video->updated_at->setTimezone('Asia/Taipei') }}">{{ $video->updated_at->diffForHumans() }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <script>
      @php($temp = $videos->where('hidden', false)->take(54)->reverse())

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
          legend: {
            display: false,
          },
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
    </script>
  </body>
</html>
