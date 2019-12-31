<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->name }} | YouTuber</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
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

      <div style="padding: 10px; overflow-x: scroll;">
        <div style="width: 100%;">
          <canvas height="500" style="width: {{ $statistics->count() * 7 }}px; min-width: 100%"></canvas>
        </div>
      </div>
    </div>

    <script>
      new Chart(document.querySelector('canvas').getContext('2d'), {
        type: 'line',
        data: {
          labels: @json($statistics->pluck('fetched_at')->map->setTimezone('Asia/Taipei')->map->toDateTimeString()),
          datasets: [{
            label: '觀看數',
            data: @json($statistics->pluck('views')),
            backgroundColor: 'rgba(54, 162, 235, .2)',
            borderColor: 'rgb(54, 162, 235)',
          }],
        },
        options: {
          responsive: {{ app('router')->is('video.date') ? 'false' : 'true' }},
          maintainAspectRatio: false,
          hover: {
            mode: 'nearest',
            intersect: true,
          },
          tooltips: {
            mode: 'index',
            intersect: false,
            callbacks: {
              label: function(tooltipItem, data) {
                const name = data.datasets[tooltipItem.datasetIndex].label;
                const val = tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return `${name}: ${val}`;
              },
              afterLabel: function(tooltipItem, data) {
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
            yAxes: [{
              ticks: {
                callback: (val) => val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
              },
            }],
          },
        },
      });
    </script>
  </body>
</html>
