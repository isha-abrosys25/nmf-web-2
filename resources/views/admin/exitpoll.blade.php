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

        .toggle-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            padding: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
            max-width: 300px;
        }

        .toggle-label {
            font-weight: 500;
            color: #495057;
            margin: 0;
        }

        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .sliderr {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #6c757d;
            transition: .4s;
            border-radius: 34px;
        }

        .sliderr:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.sliderr {
            background-color: #28a745;
        }

        input:checked+.sliderr:before {
            transform: translateX(30px);
        }

        .sliderr-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: bold;
            color: white;
            pointer-events: none;
        }

        .sliderr-text.on {
            left: 8px;
            display: none;
        }

        .sliderr-text.off {
            right: 8px;
        }

        input:checked+.sliderr .sliderr-text.on {
            display: block;
        }

        input:checked+.sliderr .sliderr-text.off {
            display: none;
        }

        /* Status indicator */
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-on {
            background-color: #28a745;
        }

        .status-off {
            background-color: #6c757d;
        }

        /* Animation for status text */
        .status-text {
            transition: color 0.3s ease;
            margin-top: 3px;
            margin-left: 10px;
        }

        .status-on-text {
            color: #28a745;

        }

        .status-off-text {
            color: #6c757d;
        }
    </style>

    @php
        use App\Models\HomeSection;
        $liveStatus = HomeSection::where('title', 'ExitPollSection')->value('status') ?? 0;
    @endphp
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- /.row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center px-5 py-2.5">
                                <h3 class="card-title mb-0 fs-5">Exit Poll</h3>

                            </div>
                            {{-- liveStatus --}}
                            <div class="row pt-4 px-3">
                                <div class="checkbox-wrapper-46">
                                    <div class="d-flex align-item-center">
                                        <form action="{{ route('toggle.exit.poll') }}" method="POST" class="d-inline">
                                            @csrf
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="live_status" {{ $liveStatus ? 'checked' : '' }}
                                                    onchange="this.form.submit()">
                                                <span class="sliderr">
                                                    <span class="sliderr-text on">ON</span>
                                                    <span class="sliderr-text off">OFF</span>
                                                </span>
                                            </label>
                                        </form>
                                        <div class="status-text {{ $liveStatus ? 'status-on-text' : 'status-off-text' }}">
                                            <span
                                                class="status-indicator {{ $liveStatus ? 'status-on' : 'status-off' }}"></span>
                                            {{ $liveStatus ? 'Visible' : 'Hidden' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       

                        <form action="{{ route('exitpollsave') }}" method="post" id="exitpollForm">
                            @csrf

                            @if (session('success'))
                                <div id="successMessage" class="alert alert-success text-center mb-2">
                                    {{ session('success') }}
                                </div>
                            @else
                                <div id="successMessage" class="alert alert-success text-center mb-2" style="display:none;">
                                </div>
                            @endif

                            <!-- Global error area -->
                            <div id="totalSeatsError" style="display:none;" class="alert alert-danger text-center mb-3">
                            </div>
                            <div class="card-body py-2">
                                <table class="table px-2 text-nowrap table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Party</th>
                                            <th>Exit Poll</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $party)
                                            <tr>
                                                <td>{{ $party->abbreviation }}</td>
                                                <td>
                                                    <input type="number" class="form-control seat-input" placeholder="exit-poll"
                                                        name="{{ strtolower($party->abbreviation) }}_wl"
                                                        value="{{ old(strtolower($party->abbreviation) . '_wl', $party->exit_poll ?? 0) }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center p-3">
                                <button type="submit" class="--btn pb-2" style="background: #28364f;">Submit</button>
                            </div>
                        </form>


                        <div class="col-sm-12 col-md-7 d-flex justify-content-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTableExample_paginate">
                                <ul class="pagination">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
    </div>
    </div>
    </section>
    </div>
    @push('custom-scripts')
    @endpush
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const MAX_SEATS = 243;
        const form = document.getElementById('voteForm');
        const inputsSelector = '.seat-input'; // only count W+L fields
        const inputs = () => Array.from(document.querySelectorAll(inputsSelector));
        const errorBox = document.getElementById('totalSeatsError');
        const successBox = document.getElementById('successMessage'); // success message div

        // compute total seats (treat invalid/empty as 0)
        function computeTotal() {
            return inputs().reduce((sum, el) => {
                const v = parseInt(el.value, 10);
                return sum + (isNaN(v) ? 0 : v);
            }, 0);
        }

        // show/hide error and mark inputs
        function validateAndDisplay() {
            const total = computeTotal();

            if (total > MAX_SEATS) {
                errorBox.textContent =
                    ` Total seats exceed limit (${MAX_SEATS}). Current total: ${total}. Please adjust values.`;
                errorBox.style.display = 'block';
                inputs().forEach(i => i.classList.add('seat-invalid'));
                return false;
            } else {
                errorBox.style.display = 'none';
                inputs().forEach(i => i.classList.remove('seat-invalid'));
                return true;
            }
        }

        // live validation
        document.addEventListener('input', function(ev) {
            if (ev.target && ev.target.matches(inputsSelector)) {
                let val = ev.target.value;
                if (val !== '' && Number(val) < 0) {
                    ev.target.value = Math.abs(Number(val));
                }
                validateAndDisplay();
            }
        }, true);

        document.addEventListener('blur', function(ev) {
            if (ev.target && ev.target.matches(inputsSelector)) {
                validateAndDisplay();
            }
        }, true);

        // on form submit
        form.addEventListener('submit', function(e) {
            if (!validateAndDisplay()) {
                e.preventDefault();
                const first = inputs().find(i => i);
                if (first) first.focus();
            } else {
                // show success message dynamically
                if (successBox) {
                    successBox.textContent = "Form submitted successfully! ✅";
                    successBox.style.display = "block";
                    successBox.style.opacity = "1";

                    // fade out after 3s
                    setTimeout(() => {
                        successBox.style.transition = "opacity 0.5s";
                        successBox.style.opacity = "0";
                        setTimeout(() => successBox.remove(), 500);
                    }, 3000);
                }
            }
        });

        // initial check
        validateAndDisplay();
    });
</script>
