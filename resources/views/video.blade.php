<!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->name }} | YouTuber</title>
    <link href="{{ asset('/css/app.css')  }}" rel="stylesheet">
    <link href="{{ asset('/css/chart.min.css')  }}" rel="stylesheet">
    <script src="{{ asset('/js/chart.min.js') }}"></script>
    <script src="{{ asset('/js/hammer.min.js') }}"></script>
    <script src="{{ asset('/js/chartjs-plugin-zoom.min.js') }}"></script>
  </head>
  <body>
    <div>
      <h1>{{ $video->name }}</h1>

      <a href="{{ route('channel', ['channel' => $channel->uid]) }}">{{ $channel->name }}</a>
      @include('components.dot')
      <span>發佈於：{{ $video->published_at->setTimezone('Asia/Taipei') }}（{{ $video->published_at->diffForHumans() }}）</span>
      @include('components.dot')
      <span>觀看數：{{ number_format($video->views) }}</span>
      @include('components.dot')
      @component('components.external-link')
        @slot('href', sprintf('https://www.youtube.com/watch?v=%s', $video->uid))

        YouTube
      @endcomponent

      <hr>

      @foreach (['views', 'comments', 'likes'] as $type)
        <div class="graph mt-1 mb-1">
          <canvas id="{{ $type }}"></canvas>
        </div>
      @endforeach
    </div>

    @if(app('router')->is('video'))
      @php($statistics = $statistics->unique('views'))
    @endif

    <script>
      const formatNum = (val) => val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

      const labels = @json($statistics->pluck('fetched_at')->map->setTimezone('Asia/Taipei')->map->toDateTimeString());

      const ticks = {
        callback: formatNum,
        precision: 0,
      };

      const zoom = {
        enabled: true,
        mode: 'x',
        speed: 0.05,
        rangeMin: {
          x: labels.length ? Date.parse(labels[0]) : null,
          y: null,
        },
        rangeMax: {
          x: labels.length ? Date.parse(labels[labels.length - 1]) : null,
          y: null,
        },
      };

      const options = {
        maintainAspectRatio: false,
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: (tooltipItem, data) => {
              return `${data.datasets[tooltipItem.datasetIndex].label}: ${formatNum(tooltipItem.yLabel)}`;
            },
            afterLabel: (tooltipItem, data) => {
              const idx = tooltipItem.index;
              const values = data.datasets[tooltipItem.datasetIndex].data;

              let inc = '不適用';
              let time = '不適用';

              if (idx > 0) {
                inc = values[idx] - values[idx - 1];
                inc = inc >= 0 ? `+${inc}` : inc;
                time = Date.parse(data.labels[idx].replace(' ', 'T')) - Date.parse(data.labels[idx - 1].replace(' ', 'T'));
                time = `${(time / 1000 / 60).toFixed(0)} 分鐘`;
              }

              return `增長數: ${inc}\n時間差: ${time}`;
            }
          },
        },
        scales: {
          xAxes: [{
            type: 'time',
            distribution: 'linear',
            time: {
              displayFormats: {
                minute: 'MM/DD HH:mm',
                hour: 'MM/DD HH:00'
              },
              unit: {!! $statistics->count() > 5000 ? "'hour'" : "'minute'" !!},
            },
            ticks: {
              autoSkip: true,
            },
          }],
          yAxes: [{ ticks }],
        },
        plugins: {
          zoom: {
            pan: zoom,
            zoom: zoom,
          },
        },
      };

      const style = {
        backgroundColor: 'rgba(54, 162, 235, .2)',
        borderColor: 'rgb(54, 162, 235)',
        borderWidth: 2,
        fill: false,
        pointRadius: 0,
      };

      new Chart(document.querySelector('#views').getContext('2d'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: '觀看數',
            data: @json($statistics->pluck('views')),
            ...style,
          }],
        },
        options,
      });

      new Chart(document.querySelector('#comments').getContext('2d'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: '留言數',
            data: @json($statistics->pluck('comments')),
            ...style,
          }],
        },
        options,
      });

      new Chart(document.querySelector('#likes').getContext('2d'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: '喜歡數',
            data: @json($statistics->pluck('likes')),
            ...style,
          }, {
            label: '不喜歡數',
            data: @json($statistics->pluck('dislikes')),
            yAxisID: 'y-dislikes',
            ...Object.assign(style, {
              backgroundColor: 'rgba(235,97,26,0.2)',
              borderColor: 'rgb(235,96,25)',
            }),
          }],
        },
        options: Object.assign(options, Object.assign(options.scales, Object.assign(options.scales.yAxes, {
          yAxes: [{
            ticks,
          }, {
            position: 'right',
            id: 'y-dislikes',
            ticks,
          }],
        }))),
      });
    </script>
  </body>
</html>
