@extends('layouts.base')

@section('title', sprintf('%s | YouTuber', $channel->name))

@section('header')
  <section class="d-flex align-items-center">
    @if ($channel->thumbnail)
      @component('components.image')
        @slot('alt', $channel->name)
        @slot('class', 'mr-2')
        @slot('height', 40)
        @slot('src', route('ggpht', ['payload' => bin2hex(app('aes')->encrypt($channel->thumbnail))]))
        @slot('width', 40)
      @endcomponent
    @endif

      <h1>{{ $channel->name }}</h1>
  </section>
@endsection

@section('main')
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

  <hr>

  <h2>影片觀看數走勢</h2>

  <section class="mt-2 mb-3 channel-recently-trend">
    @include('components.canvas')
  </section>

  <h2>數據統計</h2>

  <section class="mt-2 table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
        <tr class="text-center">
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
            <td class="text-center">近 {{ sprintf('%02d', $num) }} 部平均</td>
            <td class="text-right">{{ number_format($temp->avg('views')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('comments')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('likes')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('dislikes')) }}</td>
          </tr>
        @endforeach

        @foreach (range(1, 3) as $time)
          @php($temp = $videos->where('hidden', false)->where('published_at', '>=', now()->subMonths($time)))

          @continue($temp->isEmpty())

          <tr>
            <td class="text-center">近 {{ $time }} 個月平均（{{ sprintf('%02d', $temp->count()) }} 部）</td>
            <td class="text-right">{{ number_format($temp->avg('views')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('comments')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('likes')) }}</td>
            <td class="text-right">{{ number_format($temp->avg('dislikes')) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>

  <h2>影片列表</h2>

  <section class="mt-2 table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
        <tr class="text-center">
          <th>#</th>
          <th class="text-left">影片名稱</th>
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
            <td class="text-center">
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
            <td class="text-right">{{ number_format($video->views) }}</td>
            <td class="text-right">{{ number_format($video->comments) }}</td>
            <td class="text-right">{{ number_format($video->likes) }}</td>
            <td class="text-right">{{ number_format($video->dislikes) }}</td>
            <td class="text-right" title="{{ $video->published_at->setTimezone('Asia/Taipei') }}">{{ $video->published_at->diffForHumans() }}</td>
            <td class="text-right" title="{{ $video->updated_at->setTimezone('Asia/Taipei') }}">{{ $video->updated_at->diffForHumans() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection

@section('style')
  <link rel="stylesheet" href="{{ asset('/css/chart.min.css?v=2.9.3')  }}">
@endsection

@section('script')
  @php($temp = $videos->where('hidden', false)->take(54)->reverse())

  <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
    var labels = @json($temp->pluck('name'));
    var data = @json($temp->pluck('views'));
  </script>

  <script src="{{ asset('/js/chart.min.js?v=2.9.3') }}" defer></script>
  <script src="{{ mix('/js/channel.js') }}" defer></script>
@endsection
