@extends('adminlte::page')

@section('title', 'Master Tiket')

@section('content_header')
<h1>Master Tiket</h1>
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
                    List Tiket
                    @can('tiket-create')
                    <a class="btn btn-success btn-sm" href="{{ route('tiket.create') }}"> Create New Tiket</a>
                    @endcan
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-responsive table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Subject</th>
                                    <th width="10%">Departement</th>
                                    <th width="10%">Status</th>
                                    <th width="15%"> Last Update</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $user)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->subject }}</td>
                                    <td>{{ $user->departementName }}</td>
                                    <td>{{ $user->statusName }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->updated_at)->format('d-m-Y h:i:s')}}</td>
                                    <td>
                                        <a class="btn btn-info btn-xs" href="{{ route('tiket.show',$user->id) }}">Reply</a>

                                        @can('Tiket-delete')
                                        {!! Form::open(['method' => 'DELETE','route' => ['Tiket.destroy', $user->id],'style'=>'display:inline']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}
                                        {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! $data->render() !!}

        </div>
    </div>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable({
            paging: false,
        });
    });
</script>
@stop