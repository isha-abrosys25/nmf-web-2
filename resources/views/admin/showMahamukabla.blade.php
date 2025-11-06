@extends('layouts.adminNew')
@push('style')
    <link href="{{ asset('asset/new_admin/css/main_style.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <style>
        /* action buttons */
        .action_btn {
            padding-block: 3.5px;
            padding-inline: 8.5px;
            border-radius: 4px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;

        }
        .btn_view {
            background-color: #0381cf;

            &:hover {
                background-color: #0577bd;
                color: #fff;
            }

        }

        .btn_edit {
            background-color: #0381cf;
            margin-left: 1.5px
        }

        .btn_edit:hover {
            background-color: #0577bd;
            color: #fff;
        }

        .btn_delete {
            background-color: #ff0000;
            margin-left: 1.5px
        }

        .btn_delete:hover {
            background-color: #ca0808;
            color: #fff;
        }

        .status-unpublished {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-archive {
            background-color: #e0e0e0;
            color: #555;
        }

        .status-unknown {
            background-color: #e2e3e5;
            color: #6c757d;
        }

        .btn-dngr {
            background: #f74c4c;
            color: #fff;
        }

        .btn-dngr:hover {
            background: #ff0000;
            color: #fff;
        }

        @media (max-width: 500px) {
            .btn_edit {
                margin-left: 5px
            }

            .btn_delete {
                margin-left: 5px
            }
        }



        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .tb-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* smooth scroll on mobile */
        }

        .el-table {
            min-width: 600px;
            /* ensures scrollable area */
            border-collapse: collapse;
        }

        .btn-success {
            background: #28a745
        }

        .toggle-label {
            font-weight: 600;
            color: #495057;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toggle-label i {
            color: #6f42c1;
        }

        /* Maha Toggle Switch Styles */
        .maha-toggle-switch {
            position: relative;
            display: inline-block;
            width: 68px;
            height: 34px;
        }

        .maha-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .maha-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, #6c757d, #8e9aaf);
            transition: .4s;
            border-radius: 34px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .maha-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background: linear-gradient(135deg, #ffffff, #f1f3f5);
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        input:checked+.maha-slider {
            background: #28a745;
        }

        input:checked+.maha-slider:before {
            transform: translateX(34px);
        }

        .maha-slider-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: bold;
            color: white;
            pointer-events: none;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        .maha-slider-text.on {
            left: 10px;
            display: none;
        }

        .maha-slider-text.off {
            right: 10px;
        }

        input:checked+.maha-slider .maha-slider-text.on {
            display: block;
        }

        input:checked+.maha-slider .maha-slider-text.off {
            display: none;
        }

        /* Status indicator */
        .maha-status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
        }

        .maha-status-on {
            background-color: #28a745;
        }

        .maha-status-off {
            background: linear-gradient(135deg, #6c757d, #8e9aaf);
        }

        /* Animation for status text */
        .maha-status-text {
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 2px;
        }

        .maha-status-on-text {
            color: #28a745;
        }

        .maha-status-off-text {
            color: #6c757d;
        }

        /* Form styling */
        .maha-toggle-form {
            margin: 0;
        }

        /* Preview section styling */
        .preview-section {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .maha-section-content {
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .hidden {
            display: none;
        }

        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    @php
        use App\Models\HomeSection;
        $mahaStatus = HomeSection::where('title', 'ElectionMahaSection')->value('status') ?? 0;

    @endphp

    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center px-5 py-2.5">
                                <h3 class="card-title mb-0 fs-5">Mahamuqabla list</h3>

                                <a href="{{ route('mahamukabla.create') }}" class="--btn pt-2"
                                    style="background:#28364f">Add
                                    Mahamuqabla</a>

                            </div>
                            <div class="row px-sm-3 pt-sm-3 px-0 pt-0">
                                {{-- mahaStatus --}}
                                <div class="row">
                                    <div class="checkbox-wrapper-46 d-flex mb-2">

                                        <form action="{{ route('toggle.maha.section') }}" method="POST"
                                            class="maha-toggle-form">
                                            @csrf
                                            <label class="maha-toggle-switch">
                                                <input type="checkbox" name="maha_status" id="mahaToggle"
                                                    {{ $mahaStatus ? 'checked' : '' }}
                                                    onchange="this.form.submit(); updateMahaSection()">
                                                <span class="maha-slider">
                                                    <span class="maha-slider-text on">ON</span>
                                                    <span class="maha-slider-text off">OFF</span>
                                                </span>
                                            </label>
                                        </form>
                                        <div class="maha-status-text {{ $mahaStatus ? 'maha-status-on-text' : 'maha-status-off-text' }}"
                                            id="mahaStatusText">
                                            <span
                                                class="maha-status-indicator {{ $mahaStatus ? 'maha-status-on' : 'maha-status-off' }}"></span>
                                            {{ $mahaStatus ? 'Visible' : 'Hidden' }}
                                        </div>

                                    </div>

                                </div>

                                <div class="tb-responsive">
                                    <table class="table el-table text-nowrap">
                                        <thead>
                                            <tr>
                                                {{-- <th>Enable</th> --}}
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Image Name</th>
                                                {{-- <th>Status</th> --}}
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($mahamukablas as $slide)
                                                <tr>
                                                    {{-- <td>
                                                        <form action="{{ route('mahamukabla.toggle', $slide->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <label class="switch">
                                                                <input type="checkbox" name="status"
                                                                    onchange="this.form.submit()"
                                                                    {{ $slide->status ? 'checked' : '' }}>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </form>
                                                    </td> --}}
                                                    <td>{{ $slide->id }}</td>
                                                    <td>
                                                        @if ($slide->slide_image)
                                                            <img src="{{ asset($slide->slide_image) }}" width="80"
                                                                height="60"
                                                                style="width:110px; height:55px; object-fit: cover; border-radius: 5px;">
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ basename($slide->slide_image) }}</td>
                                                    {{-- <td>
                                                        <span
                                                            class="status {{ $slide->status ? 'status-active' : 'status-inactive' }}">
                                                            {{ $slide->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td> --}}
                                                    <td>
                                                        <form action="{{ route('mahamukabla.destroy', $slide->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this slide?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="act_btn p-0 border-0" type="submit"><i
                                                                    class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No slide images found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">


    </div>
@endsection
