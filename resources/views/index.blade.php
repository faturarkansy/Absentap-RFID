<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta name="description" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">

    <title>{{ $config['title'] }}</title>
    <base href="{{ url('') }}" />
    <meta name="title" content="{{ $config['title'] }}" />
    <meta name="description" content="{{ $config['description'] }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
    <link rel="manifest" href="images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#3a6073">
    <meta name="msapplication-TileImage" content="images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#3a6073">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="styles/landing-vendor.css">
    <link rel="stylesheet" href="styles/landing.css">
    <script src="scripts/modernizr.js"></script>
</head>

<body>
    <main>
        <header>
            <div class="container d-flex justify-content-between align-items-center">
                <div class="logo">
                    <div class="image">
                        <img src="{{ $config['first_logo'] }}" alt="Logo One">
                        @if (!empty($config['second_logo']) && $config['second_logo'] != 'images/logomsonly.png')
                            <img src="{{ $config['second_logo'] }}" class="ms-1" alt="Logo One">
                        @endif
                    </div>
                    <div class="text">
                        <h1>{{ $config['first_title'] }}</h1>
                        <h1>{{ $config['second_title'] }}</h1>
                    </div>
                </div>
                <div class="timeContainer">
                    <div id="dateDisplay" class="dateDisplay"></div>
                    <div id="timeDisplay" class="timeDisplay"></div>
                </div>
            </div>
        </header>
        <section class="body">
            <div class="container">
                <div class="info-student" id="infoStudent">
                </div>
                <form action="send-attendance" id="formAjax">
                    @csrf
                    <h1>Silahkan Tap Kartu Siswa Anda</h1>
                    <img src="images/tapcard.png" alt="">
                    <input name="code" type="text" id="idNumber" autocomplete="off" autofocus>
                </form>
            </div>
        </section>
        <footer>
            <div class="container">
                <p class="text-center mb-0 pb-2">Developed by <img src="images/logoms.png"
                        style="height: 20px; width: 20px;" alt=""> mudahsaja.id</p>
            </div>
        </footer>
    </main>
    <script src="scripts/landing-vendor.js"></script>
    <script src="scripts/landing.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(() => {
                $("#formAjax #idNumber").focus();
            }, 1000);
            $("#formAjax #idNumber").focus();
            $("#formAjax").submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let btnSubmit = form.find("[type='submit']");
                let btnSubmitHtml = btnSubmit.html();
                let url = form.attr("action");
                let data = new FormData(this);
                $.ajax({
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
                        if (response.status === "success") {
                            $('#infoStudent').html(`<h1 class="fullname">` + response.data
                                .student_name + `</h1>
                            <h1 class="nisn">` + response.data.student_nik + `</h1>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <div class="d-block m-2">` + response.data.attendance_name + `</div>
                                <h1 class="nisn m-0">` + response.data.attendance_time + `</h1>
                            </div>`);
                            $('#formAjax').addClass('d-none');
                            $("#formAjax #idNumber").val('');
                            setTimeout(() => {
                                $('#infoStudent').html('');
                                $('#formAjax').removeClass('d-none');
                                $("#formAjax #idNumber").focus();
                            }, 1000);
                        } else {
                            $('#infoStudent').html(`<div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>` + response.message + `</div>
                        </div>`);
                            $('#formAjax').addClass('d-none');
                            setTimeout(() => {
                                $("#formAjax #idNumber").val('');
                                $('#infoStudent').html('');
                                $('#formAjax').removeClass('d-none');
                                $("#formAjax #idNumber").focus();
                            }, 500);
                        }
                    },
                    error: function(response) {
                        $('#infoStudent').html(`<div class="alert alert-danger d-flex align-items-center" role="alert">
                                <div>` + response.error + `</div>
                            </div>`);
                        $('#formAjax').addClass('d-none');
                        setTimeout(() => {
                            $("#formAjax #idNumber").val('');
                            $('#infoStudent').html('');
                            $('#formAjax').removeClass('d-none');
                            $("#formAjax #idNumber").focus();
                        }, 1500);
                    }
                });
            });
        })
    </script>
</body>

</html>
