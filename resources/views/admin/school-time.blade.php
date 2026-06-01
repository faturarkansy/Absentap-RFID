@extends('admin.layouts.main')

@section('importheadAppend')
    <link rel="stylesheet" href="styles/select2.min.css">
    <link rel="stylesheet" href="styles/flatpickr.min.css">
    <link rel="stylesheet" href="styles/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <h3 class="content-header-title mb-0">School Start Times</h3>
                <div class="row breadcrumbs-top">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            @foreach ($breadcrumbs as $item)
                                @if (!$item['disabled'])
                                    <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                                @else
                                    <li class="breadcrumb-item active">{{ $item['title'] }}</li>
                                @endif
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div
                        class="col-md-6 col-12 d-flex justify-content-center justify-content-md-start align-items-center mb-3 mb-md-0">
                        <h3 class="card-title">Data School Start Times</h3>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-center justify-content-md-end mb-md-0 mb-2">
                        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                            <button class="btn btn-outline-primary" id="refreshTable" type="button"><i
                                    class="fas fa-arrows-rotate"></i> Refresh</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalView"
                                type="button"><i class="fas fa-plus"></i> Add New</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <button id="deleteSelected" class="btn btn-sm btn-danger" disabled><i class="fas fa-trash"></i> Delete
                        Selected</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-bottom w-100" id="Datatable">
                    <thead>
                        <tr>
                            <th style="width: 30px"><input type="checkbox" id="checkAll"></th>
                            <th>Action</th>
                            <th>Name</th>
                            <th>Times (24 <i class="fa-solid fa-clock ms-1"></i>)</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewLabel">School Start Time</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formView" action="admin/school-time">
                    @csrf
                    <div class="modal-body">
                        <div class=" mb-3">
                            <label for="nameView">Name Times <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="nameView" class="form-control form-control-sm" name="name"
                                    required>
                                <span class="input-group-text"><i class="fa-solid fa-pen ms-1"></i></span>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="timesStartView">Start Times <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="timesStartView" name="time_limit_start"
                                        style="border-radius:.25rem 0 0 .25rem; width: 150px" required>
                                    <span class="input-group-text">24 <i class="fa-solid fa-clock ms-1"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="timesEndView">End Times <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="timesEndView" name="time_limit_end"
                                        style="border-radius:.25rem 0 0 .25rem; width: 150px" required>
                                    <span class="input-group-text">24 <i class="fa-solid fa-clock ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('importfootAppend')
    <script src="scripts/select2.min.js"></script>
    <script src="scripts/flatpickr.min.js"></script>
    <script src="scripts/flatpickr-id.js"></script>
    <script src="scripts/jquery.dataTables.min.js"></script>
    <script src="scripts/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#timesStartView').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: "07:15",
                minuteIncrement: 1,
                static: true
            });

            $('#timesEndView').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: "07:15",
                minuteIncrement: 1,
                static: true
            });

            $('#refreshTable').click(function() {
                dataTable.draw();
            });

            let dataTable = $('#Datatable').DataTable({
                responsive: true,
                scrollX: false,
                processing: true,
                serverSide: true,
                order: [
                    [3, 'asc']
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                pageLength: 10,
                ajax: {
                    url: "admin/school-time"
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'time_limit_start',
                        name: 'time_limit_start',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return full.time_limit_start + ' - ' + full.time_limit_end;
                        },
                    },
                ],
            });

            let modalView = document.getElementById('modalView');
            const bsView = new bootstrap.Modal(modalView);

            modalView.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget
                let id = button.getAttribute('data-bs-id');
                let name = button.getAttribute('data-bs-name');
                let time_start = button.getAttribute('data-bs-time_start');
                let time_end = button.getAttribute('data-bs-time_end');
                let category = button.getAttribute('data-bs-category');
                let group = button.getAttribute('data-bs-group');
                var element = document.querySelector('#formView');
                if (id == '' || id == null) {
                    element.setAttribute('action', 'admin/school-time');
                    $('#nameView').val('');
                    $('#timesStartView').val('');
                    $('#timesEndView').val('');
                } else {
                    element.setAttribute('action', 'admin/school-time/' + id);
                    $('#nameView').val(name);
                    $('#timesStartView').val(time_start);
                    $('#timesEndView').val(time_end);
                }
            });

            modalView.addEventListener('hidden.bs.modal', function(event) {
                var element = document.querySelector('#formView');
                element.setAttribute('action', 'admin/school-time');
                $('#nameView').val('');
                $('#timesStartView').val('');
                $('#timesEndView').val('');
            });

            $("#formView").submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let btnSubmit = form.find("[type='submit']");
                let btnSubmitHtml = btnSubmit.html();
                let url = form.attr("action");
                let data = new FormData(this);
                $.ajax({
                    beforeSend: function() {
                        btnSubmit.addClass("disabled").html(
                            "<span aria-hidden='true' class='spinner-border spinner-border-sm' role='status'></span> Loading ..."
                        ).prop("disabled", "disabled");
                    },
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: "POST",
                    url: 'admin/school-time/delete',
                    data: {
                        id: ids
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr(
                            "disabled");
                        if (response.status === "success") {
                            toastr.success(response.message, 'Success !', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            dataTable.draw();
                            bsView.hide();
                        } else {
                            if (response.error) {
                                toastr.error(("Duplicated Entry"), 'Failed !', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 1500
                                });
                            } else {
                                toastr.error((response.message ? response.message :
                                    "Please complete your form"), 'Failed !', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 1500
                                });
                            }
                            dataTable.draw();
                            bsView.hide();
                        }
                    },
                    error: function(response) {
                        btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr(
                            "disabled");
                        toastr.error(response.responseJSON.message, 'Failed !', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 1500
                        });
                        dataTable.draw();
                        bsView.hide();
                    }
                });
            });
            $('#checkAll').on('click', function() {
                var rows = dataTable.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
                toggleDeleteButton();
            });

            $('#Datatable tbody').on('change', 'input[type="checkbox"]', function() {
                if (!this.checked) {
                    var el = $('#checkAll').get(0);
                    if (el && el.checked && ('indeterminate' in el)) {
                        el.indeterminate = true;
                    }
                }
                toggleDeleteButton();
            });

            $('#deleteSelected').on('click', function() {
                var ids = [];

                $('.schooltime_checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: 'admin/school-time/delete',
                        data: {
                            id: ids
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function(response) {
                            if (response.status === "success") {
                                toastr.success(response.message, 'Success !', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 1500
                                });
                                dataTable.draw();
                            } else {
                                toastr.error((response.message ? response.message :
                                    "Please complete your form"), 'Failed !', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 1500
                                });
                            }
                        },
                        error: function(response) {
                            toastr.error(response.responseJSON.message, 'Failed !', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                        }
                    });
                    $('#deleteSelected').prop('disabled', true);
                }
            });

            function toggleDeleteButton() {
                if ($('.schooltime:checked').length > 0) {
                    $('#deleteSelected').prop('disabled', false);
                } else {
                    $('#deleteSelected').prop('disabled', true);
                }
            }
        });
    </script>
@endsection
