@extends('layouts.adminNew')

@section('content')
    @push('style')
        <link href="{{ asset('asset/new_admin/css/main_style.css') }}" rel="stylesheet" />
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <div class=" mt-4">
                        <div class="card-header">
                            <h3 class="card-title mb-2 fs-5">Add Candidate</h3>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                            <!-- Party Selection -->
                            <div class="mb-3">
                                <label class="form-label">Select Party:</label>
                                <select name="party_id" class="form-control fc-i" required>
                                    <option value="">-- Select Party --</option>
                                    @foreach ($parties as $party)
                                        <option value="{{ $party->id }}"
                                            {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                            {{ $party->party_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Candidate Name -->
                            <div class="mb-3">
                                <label class="form-label">Candidate Name:</label>
                                <select name="candidate_name" class="form-control fc-i" required>
                                    <option value="">-- Select Candidate --</option>
                                    @foreach (config('global.candidate_names') as $name)
                                        <option value="{{ $name }}"
                                            {{ old('candidate_name') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Candidate Image -->
                            <div class="mb-3">
                                <label class="form-label">Candidate Image:</label>
                                <input type="file" name="candidate_image" class="form-control fc-i">
								<small class="text-danger d-block mt-1">
                                    * Recommended size: 100×100 px (1:1 aspect ratio)
                                </small>
                                @if (old('candidate_image'))
                                    <img src="{{ asset(old('candidate_image')) }}" alt="Candidate Preview" width="100"
                                        class="mt-2">
                                @endif
                            </div>

                            <!-- Area Selection -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Area:</label>
                                <select name="area" class="form-control border fc-i" required>
                                    <option value="">-- Select Area --</option>
                                    @foreach (config('global.area') as $area)
                                        <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="--btn bg-dark fs-4 pb-2 px-4 text-white">Submit</button>
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
