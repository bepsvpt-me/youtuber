@extends('layouts.base')

@section('header')
  <section class="d-flex align-items-center justify-content-between">
    <h1>YouTuber</h1>

    <a
      class="d-flex align-items-center"
      href="{{ route('trending') }}"
    >
      <svg class="link-icon" viewBox="0 0 24 24">
        <path fill="currentColor" d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z" />
      </svg>

      <span class="ml-1">發燒趨勢</span>
    </a>
  </section>
@endsection

@section('main')
  <section class="mt-2 table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
        <tr class="text-center">
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
          <tr class="text-center">
            <td>
              @if($channel->thumbnail)
                @component('components.image')
                  @slot('alt', $channel->name)
                  @slot('src', route('ggpht', ['payload' => bin2hex(app('aes')->encrypt($channel->thumbnail))]))
                @endcomponent
              @else
                <span>{{ $idx + 1 }}</span>
              @endif
            </td>

            <td class="text-left">
              <a href="{{ route('channel', ['channel' => $channel->uid]) }}">{{ $channel->name }}</a>
            </td>

            <td class="text-right">{{ number_format($channel->subscribers) }}</td>

            <td class="text-right">{{ number_format($channel->views) }}</td>

            <td class="text-right">{{ number_format($channel->videos) }}</td>

            <td>{{ $channel->published_at->setTimezone('Asia/Taipei') }}</td>

            <td>{{ $channel->updated_at->setTimezone('Asia/Taipei') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
