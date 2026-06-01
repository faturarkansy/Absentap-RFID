@extends('admin.layouts.main')

@section('importheadAppend')
    <link rel="stylesheet" href="styles/select2.min.css">
    <link rel="stylesheet" href="styles/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <h3 class="content-header-title mb-0">Students</h3>
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
                        <h3 class="card-title">Data Students</h3>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-center justify-content-md-end mb-md-0 mb-2">
                        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                            <button class="btn btn-outline-primary" id="refreshTable" type="button"><i
                                    class="fas fa-arrows-rotate"></i> Refresh</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4 align-items-end">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="activeSelect">Status <span class="text-danger">*</span></label>
                        <select class="form-select form-control form-control-sm" id="selectActive" name="active"
                            style="width: 100%;">
                            <option value="0" selected>All</option>
                            <option value="non_active">Not Active</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalImport"><i class="fa-solid fa-file-excel"></i> IMPORT EXCEL</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-bottom w-100" id="Datatable">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Name</th>
                            <th>NIK</th>
                            <th>Card Number</th>
                            <th>Rombel</th>
                            <th>Status</th>
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
                    <h5 class="modal-title" id="modalViewLabel">Student Data Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formView" action="admin/students/">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img src="" alt="" id="imageView" style="width: 150px; border-radius: 10px;">
                        </div>
                        <table class="table">
                            <tr>
                                <td style="width: 100px;">Fullname</td>
                                <td style="width: 5px;">:</td>
                                <td id="fullnameView"></td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>:</td>
                                <td id="genderView"></td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>:</td>
                                <td id="nikView"></td>
                            </tr>
                            <tr>
                                <td>NISN</td>
                                <td>:</td>
                                <td id="nisnView"></td>
                            </tr>
                            <tr>
                                <td>Rombel</td>
                                <td>:</td>
                                <td id="rombelView"></td>
                            </tr>
                        </table>
                        <div class="mb-3">
                            <label for="number_cardView" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="number_cardView" name="card_code"
                                placeholder="Focused Here and Tap Card">
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
    <div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportLabel">Student Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImport" action="admin/students/import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Please choose excel file</label>
                            <input class="form-control" type="file" name="file" id="formFile" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('file_contoh_excel/example_file.xlsx') }}"
                                class="btn btn-outline-secondary">
                                <i class="fas fa-download"></i> Download template file
                            </a>
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
    <script src="scripts/jquery.dataTables.min.js"></script>
    <script src="scripts/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#selectActive").select2({
                placeholder: "Please select one",
                width: 'resolve',
            }).on('change', function(e) {
                dataTable.draw();
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
                    url: "admin/students",
                    data: function(d) {
                        d.active = $('#selectActive').find(':selected').val();
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'card_code',
                        name: 'card_code'
                    },
                    {
                        data: 'rombel',
                        name: 'rombel'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            let status = {
                                0: {
                                    'title': 'Not Active',
                                    'class': ' bg-danger'
                                },
                                1: {
                                    'title': 'Active',
                                    'class': ' bg-success'
                                },
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="badge bg-pill' + status[data].class + '">' +
                                status[data].title +
                                '</span>';
                        },
                    }
                ],
            });

            let modalView = document.getElementById('modalView');
            const bsView = new bootstrap.Modal(modalView);

            modalView.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget
                let id = button.getAttribute('data-bs-id');
                var element = document.querySelector('#formView');
                element.setAttribute('action', 'admin/students/' + id);
                $.ajax({
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: "GET",
                    url: 'admin/students/' + id,
                    success: function(response) {
                        // $('#imageView').attr('src', response.data.image);
                        if (response.data.jk == 'L') {
                            var image = '{{ asset('images/man.png') }}';
                            var jk = 'Laki Laki';
                        } else {
                            var image = '{{ asset('images/woman.png') }}';
                            var jk = 'Perempuan';
                        }
                        $('#imageView').attr('src', image);
                        $('#fullnameView').html(response.data.nama);
                        $('#genderView').html(jk);
                        $('#nikView').html(response.data.nik);
                        $('#nisnView').html(response.data.nisn);
                        $('#rombelView').html(response.data.rombel);
                        $('#number_cardView').val(response.data.card_code);
                        if (response.data.card_code == '' || response.data.card_code == null) {
                            setTimeout(() => {
                                $('#number_cardView').focus();
                            }, 500);
                        }
                    },
                    error: function(response) {
                        bsView.hide();
                    }
                });
            });

            modalView.addEventListener('hidden.bs.modal', function(event) {
                var element = document.querySelector('#formView');
                element.setAttribute('action', 'admin/students/');
                $('#number_cardView').val('');
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
                            bsView.hide();
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

            let modalImport = document.getElementById('modalImport');
            const bsImport = new bootstrap.Modal(modalImport);

            $("#formImport").submit(function(e) {
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
                            bsImport.hide();
                        } else {
                            toastr.error("Import Gagal", 'Failed !', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 1500
                            });
                            dataTable.draw();
                            bsImport.hide();
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
                        bsImport.hide();
                    }
                });
            });
        });
    </script>
@endsection
