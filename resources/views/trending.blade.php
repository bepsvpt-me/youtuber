@php($fetchedAt = $videos->first()->fetched_at)
@php($carbon = \Carbon\Carbon::parse($fetchedAt)->setSecond(0))

@extends('layouts.base')

@section('title', sprintf('YouTube Trending - %s', substr($fetchedAt, 0, -3)))

@section('header')
  <h1>YouTube Trending - {{ substr($fetchedAt, 0, -3) }}</h1>

  <section class="d-flex align-items-center justify-content-between">
    <a href="{{ route('trending.time', ['time' => $carbon->clone()->subMinutes(15)->format('Y-m-d H:i')]) }}">上一個區間</a>
    <a href="{{ route('trending.time', ['time' => $carbon->clone()->addMinutes(15)->format('Y-m-d H:i')]) }}">下一個區間</a>
  </section>
@endsection

@section('main')
  <section class="table-responsive mt-1 mb-1">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
        <tr class="text-center">
          <th>#</th>
          <th>影片</th>
        </tr>
      </thead>

      <tbody>
        @foreach($videos as $video)
          <tr class="text-center">
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
@endsection
