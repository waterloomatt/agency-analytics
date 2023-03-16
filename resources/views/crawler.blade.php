@extends('layouts.app')

@section('title', 'Crawler')

@section('content')
    <h1>AgencyAnalytics - Crawler</h1>
    @error('url')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <form action=""
          method="POST">
        @csrf

        <label>
            URL
            <input type="text"
                   name="url"
                   placeholder="https://agencyanalytics.com">
        </label>

        <input type="submit"
               name="submit">
    </form>



    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
@endsection