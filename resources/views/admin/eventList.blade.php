<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

@extends('layouts.adminNew')

@push('style')
    <link href="{{ asset('asset/new_admin/css/main_style.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <style>
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
        }

        .btn_view:hover {
            background-color: #0577bd;
            color: #fff;
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

        .status-unknown {
            background-color: #e2e3e5;
            color: #6c757d;
        }

        @media (max-width: 500px) {

            .btn_edit,
            .btn_delete {
                margin-left: 5px
            }
        }
    </style>

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center px-5 py-2.5">
                                <h3 class="card-title mb-0 fs-5">EVENTS</h3>
                                <div class="card-tool">
                                    <a href="{{ url('/events/add') }}" class="--btn" style="background: #28364f; padding: 9px 12px; color: #fff;">
                                        Add Event
                                    </a>
                                </div>
                            </div>

                            <div class="row pt-4 px-3 px-md-5 gap-2 gap-md-0">
                                <div class="col-sm-12 col-md-6">
                                    <form method="GET" class="d-inline-block">
                                        <label class="d-inline-flex gap-1 align-items-center">
                                            Show
                                            <select name="perPage" class="form-select select-down"
                                                onchange="this.form.submit()">
                                                <option value="30" {{ $data['perPage'] == 30 ? 'selected' : '' }}>30
                                                </option>
                                                <option value="50" {{ $data['perPage'] == 50 ? 'selected' : '' }}>50
                                                </option>
                                            </select> entries
                                        </label>
                                        <!-- Preserve filters -->
                                        <input type="hidden" name="title" value="{{ $data['title'] }}">
                                        <input type="hidden" name="author" value="{{ $data['author'] }}">
                                        <input type="hidden" name="category" value="{{ $data['category'] }}">
                                        <input type="hidden" name="status" value="{{ $data['status'] }}">
                                    </form>
                                </div>

                                <div class="col-sm-12 col-md-6 d-flex justify-content-end">
                                    <form method="GET"
                                        class="form-wrapper btn-group d-flex gap-2 flex-wrap flex-md-nowrap">
                                        <div class="group">
                                            <input id="query" class="input form-control" type="text"
                                                value="{{ $data['title'] }}" placeholder="Enter Title" name="title" />
                                        </div>

                                        <!-- Select Author -->
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" type="button">
                                                {{ $selectedAuthor ?? 'Select Author' }}
                                            </button>
                                            <ul class="dropdown-menu dp-menu">
                                                <li>
                                                    <button type="button"
                                                        onclick="document.getElementById('authorInput').value=''; this.closest('form').submit();"
                                                        class="dropdown-item">
                                                        Select Author
                                                    </button>
                                                </li>
                                                @foreach ($authors as $author)
                                                    <li>
                                                        <button type="button"
                                                            onclick="document.getElementById('authorInput').value='{{ $author->id }}'; this.closest('form').submit();"
                                                            class="dropdown-item">
                                                            {{ $author->name }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <input type="hidden" name="author" id="authorInput"
                                            value="{{ $data['author'] }}" />

                                        <!-- Select Category -->
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" type="button">
                                                {{ $selectedCategory ?? 'Select Category' }}
                                            </button>
                                            <ul class="dropdown-menu dp-menu">
                                                <li>
                                                    <button type="button"
                                                        onclick="document.getElementById('categoryInput').value=''; this.closest('form').submit();"
                                                        class="dropdown-item">
                                                        Select Category
                                                    </button>
                                                </li>
                                                @foreach ($categories as $cat)
                                                    <li>
                                                        <button type="button"
                                                            onclick="document.getElementById('categoryInput').value='{{ $cat->id }}'; this.closest('form').submit();"
                                                            class="dropdown-item">
                                                            {{ $cat->name }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <input type="hidden" name="category" id="categoryInput"
                                            value="{{ $data['category'] }}" />

                                        <!-- Preserve other filters -->
                                        <input type="hidden" name="status" value="{{ $data['status'] }}">
                                        <input type="hidden" name="perPage" value="{{ $data['perPage'] }}">

                                        <!-- Search Button -->
                                        <button class="btn btn-outline-primary" type="submit">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="card-body table-responsive py-2 px-0">
                                <table class="table article-table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>ID</th>
                                            <th>Event Name</th>
                                            <th>Author Name</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Manage</th>
                                            <th>Publish Date</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data['blogs']->count() > 0)
                                            @foreach ($data['blogs'] as $blog)
                                                <tr>
                                                    <td class="text-nowrap">
                                                        <a class="act_btn"
                                                            href="{{ url('events/edit/' . $blog->id . '?from=' . request()->segment(2) . '&status=' . $data['status'] . '&t=' . time()) }}">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $blog->id }}</td>
                                                    <td style="white-space: pre-wrap; word-wrap: break-word; width: 290px;">{{ $blog->title }}</td>
                                                    <td>{{ $blog->author->name ?? '' }}</td>
                                                    <td>{{ $blog->category->name ?? '' }}</td>
                                                    <td>
                                                        <span
                                                            class="status {{ $blog->is_active ? 'status-active' : 'status-unknown' }}">
                                                            {{ $blog->is_active ? 'Published' : 'Unknown' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <a class="act_btn"
                                                            href="{{ asset('events/event-blogs') }}/{{ $blog->id }}?t={{ time() }}"
                                                            target="_blank"><i class="fa-solid fa-sliders"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $blog->created_at }}</td>
                                                    <td class="text-nowrap">
                                                      

                                                        <a href="{{ route('deleteEvent', $blog->id) }}"
   onclick="return confirm('Are you sure you want to permanently delete this event?')"
   class="act_btn" style="margin-left:10px;"><i class="fa-solid fa-trash"></i></a>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="row pagination-block px-5">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info">
                                        @if ($data['blogs']->count() > 0)
                                            Showing {{ $data['blogs']->firstItem() }} to {{ $data['blogs']->lastItem() }}
                                            of {{ $data['blogs']->total() }} entries
                                        @else
                                            Showing 0 entries
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7 d-flex justify-content-end">
                                    {{ $data['blogs']->links() }}
                                </div>
                            </div>

                        </div> <!-- card -->
                    </div> <!-- col -->
                </div> <!-- row -->
            </div> <!-- container -->
        </section>
    </div>
@endsection
