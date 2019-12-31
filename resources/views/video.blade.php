<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->name }} | YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.4/dist/chartjs-plugin-zoom.min.js"></script>
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

      .graph {
        height: 500px;
      }
    </style>
  </head>
  <body>
    <div style="padding: 1rem;">
      <h1 style="margin-bottom: 0;">{{ $video->name }}</h1>

      <a href="{{ route('channel', ['channel' => $channel->uid]) }}" style="margin-left: 1rem;">{{ $channel->name }}</a>
      <span style="margin: 0 4px;">•</span>
      <span>發佈於：{{ $video->published_at->setTimezone('Asia/Taipei') }}（{{ $video->published_at->diffForHumans() }}）</span>
      <span style="margin-right: 4px;">•</span>
      <span>觀看數：{{ number_format($video->views) }}</span>
      <span style="margin: 0 4px;">•</span>
      <a
        href="https://www.youtube.com/watch?v={{ $video->uid }}"
        target="_blank"
        rel="noopener noreferrer"
      >
        <span>YouTube</span>

        <svg style="fill: currentColor; width: 14px; height: 14px;" viewBox="0 0 24 24">
          <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z" />
        </svg>
      </a>

      <hr>

      @foreach (['views', 'comments', 'likes'] as $type)
        <div class="graph">
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
              },
              unit: 'minute',
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
