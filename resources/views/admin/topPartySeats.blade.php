<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        @media (max-width: 500px) {
            .btn_edit {
                margin-left: 5px
            }

            .btn_delete {
                margin-left: 5px
            }
        }
    </style>
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
                                <h3 class="card-title mb-0 fs-5">Alliance wise seats</h3>
                                <div class="card-tool ">
                                    <div class="input-group input-group-sm float-right  ">
                                        <a href="{{ route('voteCount') }}" class="--btn" style="background: #28364f">
                                            Party wise seats <span class="ms-2">→</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-4 px-5">


                                <!--<div class="group">
                                                                            <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
                                                                                <g>
                                                                                    <path
                                                                                        d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z">
                                                                                    </path>
                                                                                </g>
                                                                            </svg>

                                                                            <input id="query" class="input" type="search" placeholder="Search..."
                                                                                name="searchbar" />
                                                                        </div>-->
                            </div>

                            {{-- Show Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success text-center">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger text-center">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('saveTopSeats') }}" method="post" id="seatForm">
                                @csrf

                                <!-- Global error area -->
                                <div id="totalSeatsError" style="display:none;" class="alert alert-danger text-center mb-3">
                                </div>

                                <div class="card-body py-2">
                                    <table class="table text-nowrap table-bordered">
                                        <thead class="table-primary">
                                            <tr>
                                                @foreach ($parties as $party)
                                                    <th>{{ $party->abbreviation }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach ($parties as $party)
                                                    <td>
                                                        <input type="number" class="form-control seat-input1"
                                                            placeholder="Total seats"
                                                            name="seat_{{ strtolower($party->abbreviation) }}"
                                                            value="{{ old('seat_' . strtolower($party->abbreviation), $party->seats_won) }}"
                                                            min="0" step="1"
                                                            data-party="{{ $party->abbreviation }}">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

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
        const form = document.getElementById('seatForm');
        const inputsSelector = '.seat-input1';
        const inputs = () => Array.from(document.querySelectorAll(inputsSelector));
        const errorBox = document.getElementById('totalSeatsError');

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
                // show error with exact number
                errorBox.textContent =
                    ` Total seats exceed limit (${MAX_SEATS}). Current total: ${total}. Please adjust values.`;
                errorBox.style.display = 'block';

                // mark inputs visually as invalid
                inputs().forEach(i => i.classList.add('seat-invalid'));
                return false;
            } else {
                // hide error and remove marks
                errorBox.style.display = 'none';
                inputs().forEach(i => i.classList.remove('seat-invalid'));
                return true;
            }
        }

        // Attach listeners: input (live feedback) and blur (user asked behavior)
        document.addEventListener('input', function(ev) {
            if (ev.target && ev.target.matches(inputsSelector)) {
                // optional: enforce integer >=0
                let val = ev.target.value;
                // if negative sign typed, remove it
                if (val !== '' && Number(val) < 0) {
                    ev.target.value = Math.abs(Number(val));
                }
                // live validation but not too intrusive
                validateAndDisplay();
            }
        }, true);

        document.addEventListener('blur', function(ev) {
            if (ev.target && ev.target.matches(inputsSelector)) {
                // on blur show validation (explicit)
                validateAndDisplay();
            }
        }, true);

        // Prevent form submit if invalid
        form.addEventListener('submit', function(e) {
            if (!validateAndDisplay()) {
                e.preventDefault();
                // focus the first input to help user
                const first = inputs().find(i => i);
                if (first) first.focus();
            }
        });

        // initial run (in case default values already exceed)
        validateAndDisplay();
    });
</script>
