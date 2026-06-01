@extends('admin.layouts.main')

@section('importheadAppend')
    <link rel="stylesheet" href="styles/select2.min.css">
    <link rel="stylesheet" href="styles/flatpickr.min.css">
    <link rel="stylesheet" href="styles/dataTables.bootstrap5.min.css">
    <style>
        .flatpickr-wrapper {
            width: 100%;
        }

        .icon-hadir {
            color: green;
        }

        .icon-tidakhadir {
            color: red;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <h3 class="content-header-title mb-0">Presence</h3>
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
                        <h3 class="card-title">Student Presence Data</h3>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-center justify-content-md-end mb-md-0 mb-2">
                        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                            <button class="btn btn-outline-primary" id="refreshTable" type="button"><i
                                    class="fas fa-arrows-rotate"></i> Refresh</button>
                            <button data-bs-toggle="modal" data-bs-target="#modalAdd"
                                class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-square-plus"></i> Add
                                Permission</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-bottom w-100" id="Datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Hadir <i class="fas fa-check-circle icon-hadir"></i></th>
                            <th>Tidak Hadir <i class="fas fa-times-circle icon-tidakhadir"></i></th>
                            <th>Izin Sakit</th>
                            <th>Izin</th>
                            <th>Tanpa Keterangan</th>
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
                    <h5 class="modal-title" id="modalAddLabel">Presence Permission Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHoliday" method="POST" action="admin/parent-report">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dateSelect" class="d-block">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dateSelect1" name="rec_date" required>

                        </div>
                        <div class="mb-3">
                            <label for="" class="d-block">Name <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="name" id="dateSelect2" required>
                                <span class="input-group-text"><i class="fa-solid fa-pen ms-1"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="permissionType" class="d-block">Permission Type <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="permissionType" name="kehadiran" required>
                                <option value="1">Izin Sakit</option>
                                <option value="2">Izin</option>
                                <option value="3">Tanpa Keterangan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="additionalInfo" class="d-block">Additional Information</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="additionalInfo" name="note">
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
    <script src="scripts/sweetalert2@11.js"></script>
    <script>
        $(document).ready(function() {
            $('#dateSelect1').flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d",
                mode: "range",
                static: true
            });

            $('#refreshTable').click(function() {
                dataTable.draw();
            });
            const table = $('#Datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "admin/parent-report",
                    type: 'GET',
                    dataType: 'json',
                },
                columns: [{
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'hadir_sum',
                        name: 'hadir_sum',
                        width: '12%'
                    },
                    {
                        data: 'tidakhadir_sum',
                        name: 'tidakhadir_sum',
                        width: '18%'
                    },
                    {
                        data: 'izinsakit_sum',
                        name: 'izinsakit_sum',
                        width: '12%'
                    },
                    {
                        data: 'izin_sum',
                        name: 'izin_sum',
                        width: '12%'
                    },
                    {
                        data: 'noinfo_sum',
                        name: 'noinfo_sum',
                        width: '20%'
                    },
                ],
            });

            $('#refreshTable').on('click', function() {
                table.ajax.reload(null, false);
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
                            table.ajax.reload(null, false); // Refresh the DataTable
                            $('#modalAdd').modal('hide'); // Hide the modal
                        } else {
                            if (response.error) {
                                toastr.error("Card Number Has Already Taken", 'Failed !', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 1500
                                });
                            } else {
                                toastr.error(response.message || "Please complete your form",
                                    'Failed !', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 1500
                                    });
                            }
                            table.ajax.reload(null, false); // Refresh the DataTable
                            $('#modalAdd').modal('hide'); // Hide the modal
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
                        table.ajax.reload(null, false); // Refresh the DataTable
                        $('#modalAdd').modal('hide'); // Hide the modal
                    }
                });
            });
        });
    </script>
@endsection
