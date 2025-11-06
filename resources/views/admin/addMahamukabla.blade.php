@extends('layouts.adminNew')

@section('content')
    @push('style')
        <link href="{{ asset('asset/new_admin/css/main_style.css') }}" rel="stylesheet" />
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <div class="mt-4 px-2">
                          <div class="card-header">
                            <h3 class="card-title mb-3 fs-5"> Add Mahamukabla Slide</h3>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="pt-3" action="{{ route('mahamukabla.store') }}" method="POST" enctype="multipart/form-data"
                             >
                            @csrf

                            <!-- Author Selection -->
                            {{-- <div class="mb-3">
                                <label class="form-label fw-bold">Select Author:</label>
                                <select name="author_id" class="form-control fc-i  border " required>
                                    <option value="">-- Select Author --</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- Slide Image -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Slide Image:</label>
                                <input type="file" name="slide_image" class="form-control fc-i border "
                                    required>
								<small class="text-danger d-block mt-1">
                                    * Recommended size: 420×215 px ( 16:9 aspect ratio)
                                </small>	
                                @if (old('slide_image'))
                                    <img src="{{ asset(old('slide_image')) }}" alt="Slide Preview" width="100"
                                        class="mt-2">
                                @endif
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="--btn bg-dark fs-4 pb-2 px-4 text-white">
                               Submit
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
         <style>
            .fc-i {
                color: #939393;
            }
        </style>
    @endsection
