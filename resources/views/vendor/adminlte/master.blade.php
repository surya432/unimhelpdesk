<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 2'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/Ionicons/css/ionicons.min.css') }}">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    @if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
    @endif

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">

    @if(config('adminlte.plugins.datatables'))
    <!-- DataTables with bootstrap 3 style -->
    <link rel="stylesheet" href="//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css">
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/styles/metro/notify-metro.css" />
    @yield('adminlte_css')
    <!-- Axios -->

    <!-- DataTables with bootstrap 3 renderer -->
    <!-- Config language DataTables -->


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition @yield('body_class')">

    @yield('body')



    @if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    @endif

    @if(config('adminlte.plugins.datatables'))
    <!-- DataTables with bootstrap 3 renderer -->
    <script src="//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js"></script>
    @endif

    @if(config('adminlte.plugins.chartjs'))
    <!-- ChartJS -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    @endif

    @yield('adminlte_js')
    <script type="text/javascript">
        function generateNotif(title, text, icon) {
            swal.fire({
                position: 'top-end',
                type: icon,
                title: title,
                showConfirmButton: false,
                timer: 1500
            });
        }

        $.extend($.fn.dataTable.defaults, {
            //serverSide: true,
            processing: true,
            paging:false,
            pageLength: 50,
            dom: 'Bfrtip',
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 5]
                    }
                },
            ]
        });
        $("#table").on("click", "#btnDelete", function() {
            event.preventDefault()
            var dmaIDD = $(this).attr("data-id");
            Swal.queue([{
                title: 'Apa Anda Yakin Menghapus Data?',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Cancel',
                showCloseButton: true,
                showCancelButton: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(urlAjax + "?id=" + dmaIDD)
                        .then(response => response.json())
                        .then(data => console.log(data.response))
                        .catch(() => {
                            Swal.insertQueueStep({
                                type: 'error',
                                title: 'Unable to Delete'
                            })
                        })
                }
            }]);
        });
        $("#table").on("click", "#btnEdit", function() {
            var elm = $(this);
            $('#formModal')[0].reset();
            $("input[type='hidden']").val("");
            $('#formModal').attr('method', "PUT");
            $('#formModal').attr('action', urlAjax);
            setupEditModal(elm);
            $('#modal').modal('show');
        });
        $("#formModal").on("submit", function() {
            event.preventDefault()
            var method = $('#formModal').attr('method');
            $.ajax({
                type: "post",
                url: urlAjax,
                data: $(this).serializeArray(),
                success: function(data) {
                    generateNotif(data.msg, method, data.status);
                },
                error: function(data) {
                    generateNotif(data.msg, method, data.status);
                }
            });
        });

        function reloadTable() {
            $("#table").DataTable().ajax.reload(null, false);
            generateNotif("Success", "Reload Success", "success");
        }
    </script>
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>
    
</body>

</html>