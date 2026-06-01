@extends('admin.layouts.main')

@section('importheadAppend')
    <link rel="stylesheet" href="styles/select2.min.css">
    <link rel="stylesheet" href="styles/flatpickr.min.css">
    <link rel="stylesheet" href="styles/dataTables.bootstrap5.min.css">
    <style>
        .flatpickr-wrapper {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <h3 class="content-header-title mb-0">Holiday Record</h3>
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
                        <h3 class="card-title">Data Holiday Record</h3>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-center justify-content-md-end mb-md-0 mb-2">
                        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                            <button class="btn btn-outline-primary" id="refreshTable" type="button"><i
                                    class="fas fa-arrows-rotate"></i> Refresh</button>
                            <button data-bs-toggle="modal" data-bs-target="#modalAdd"
                                class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-square-plus"></i> Add Holiday
                                Date</button>
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
                            <th>Name</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLabel">Holiday Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHoliday" method="POST" action="admin/holidays">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dateSelect" class="d-block">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dateSelect" name="date" required>

                        </div>
                        <div class="mb-3">
                            <label for="" class="d-block">Name <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="name" id="dateSelect2" required>
                                <span class="input-group-text"><i class="fa-solid fa-pen ms-1"></i></span>
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

            $('#dateSelect').flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d",
                mode: "range",
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
                    [1, 'asc']
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                pageLength: 10,
                ajax: {
                    url: "admin/holidays",
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    }
                ],
            });

            let modalAdd = document.getElementById('modalAdd');
            const bsHoliday = new bootstrap.Modal(modalAdd);

            modalAdd.addEventListener('show.bs.modal', function(event) {
                $('#dateSelect1').val('');
                $('#dateSelect2').val('');
            });

            modalAdd.addEventListener('hidden.bs.modal', function(event) {
                $('#dateSelect1').val('');
                $('#dateSelect2').val('');
            });

            $("#formHoliday").submit(function(e) {
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
                    url: url,
                    data: data,
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
                            bsHoliday.hide();
                        } else {
                            if (response.error) {
                                toastr.error(("Card Number Has Already Taken"), 'Failed !', {
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
                            bsHoliday.hide();
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
                        bsHoliday.hide();
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

                $('.holiday_checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: 'admin/holidays/delete',
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
                if ($('.holiday_checkbox:checked').length > 0) {
                    $('#deleteSelected').prop('disabled', false);
                } else {
                    $('#deleteSelected').prop('disabled', true);
                }
            }
        });
    </script>
@endsection
