@extends('adminlte::page')

@section('title', 'Master Services')

@section('content_header')
<h1>Master Text Maining</h1>
@stop

@section('content')
<div class="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif

            <div class="panel panel-primary">
                <div class="panel-heading">
                    List Perhitungan Bayes
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kata</th>
                                    <th width="15%">Topik</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("tableBayes") }}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'words'
                }, {
                    data: 'hasilPrediksi'
                }, {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }

            ]
        });
    });
</script>
@stop