@extends('layouts.app')
@section('content')
    <div class="container">
        <h2 class="page-title mb-4">🎧 Free eBook & Audiobook Samples</h2>

        <h4>📚 eBook Samples</h4>
        <div class="row mb-5">
            @foreach ($ebooks as $ebook)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>{{ $ebook->title }}</h5>
                            {{-- <a href="#" target="_blank" class="btn btn-outline-primary btn-sm mt-2">Read Sample</a> --}}
                            <a href="{{ route('epub.reader', ['id' => $ebook->id]) }}" target="_blank"
                                class="btn btn-outline-primary btn-sm mt-2">
                                Read Sample
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <h4>🎵 Audiobook Samples</h4>
        <div class="row">
            {{-- @foreach ($audiobooks as $product) --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        {{-- <h5>{{ $product->name }}</h5> --}}
                        <audio controls class="w-100 mt-2">
                            <source src="#" type="audio/mpeg">
                        </audio>
                    </div>
                </div>
            </div>
            {{-- @endforeach --}}
        </div>
    </div>
@endsection
