@php($fetchedAt = $videos->first()->fetched_at)
@php($carbon = \Carbon\Carbon::parse($fetchedAt)->setSecond(0))
<!DOCTYPE html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YouTube Trending - {{ substr($fetchedAt, 0, -3) }}</title>
    <link href="{{ mix('/css/app.css')  }}" rel="stylesheet">
  </head>
  <body>
    <h1>YouTube Trending - {{ substr($fetchedAt, 0, -3) }}</h1>

    <section style="display: flex; align-items: center; justify-content: space-between;">
      <a href="{{ route('trending.time', ['time' => $carbon->clone()->subMinutes(15)->format('Y-m-d H:i')]) }}">上一個區間</a>
      <a href="{{ route('trending.time', ['time' => $carbon->clone()->addMinutes(15)->format('Y-m-d H:i')]) }}">下一個區間</a>
    </section>

    <section class="x-scroll mt-1 mb-1">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>影片</th>
          </tr>
        </thead>

        <tbody>
          @foreach($videos as $video)
            <tr class="t-center">
              <td>{{ $video->ranking }}</td>

              <td>
                @component('components.image')
                  @slot('alt', sprintf('https://www.youtube.com/watch?v=%s', $video->vid))
                  @slot('height', 240)
                  @slot('src', route('ytimg', ['payload' => bin2hex(app('aes')->encrypt($video->vid))]))
                  @slot('width', 320)
                @endcomponent
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </section>
  </body>
</html>
