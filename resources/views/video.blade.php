@extends('layouts.base')

@section('title', sprintf('%s  | YouTuber', $video->name))

@section('header')
  <h1 class="mb-1">{{ $video->name }}</h1>

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
@endsection

@section('main')
  @foreach (['views', 'comments', 'likes'] as $type)
    <div class="graph mb-2">
      @include('components.canvas', ['id' => $type])
    </div>
  @endforeach
@endsection

@section('style')
  <link rel="stylesheet" href="{{ asset('/css/chart.min.css?v=2.9.3')  }}">
@endsection

@section('script')
  @if(app('router')->is('video'))
    @php($statistics = $statistics->unique('views'))
  @endif

  <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce() }}">
    var unit = {!! $statistics->count() > 5000 ? "'hour'" : "'minute'" !!};
    var views = @json($statistics->pluck('views'));
    var comments = @json($statistics->pluck('comments'));
    var likes = @json($statistics->pluck('likes'));
    var dislikes = @json($statistics->pluck('dislikes'));
    var labels = @json($statistics->pluck('fetched_at')->map->setTimezone('Asia/Taipei')->map->toDateTimeString());
  </script>

  <script src="{{ asset('/js/chart.min.js?v=2.9.3') }}" defer></script>
  <script src="{{ asset('/js/hammer.min.js?v=2.0.8') }}" defer></script>
  <script src="{{ asset('/js/chartjs-plugin-zoom.min.js?v=0.7.5') }}" defer></script>
  <script src="{{ mix('/js/video.js') }}" defer></script>
@endsection
