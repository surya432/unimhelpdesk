@extends('adminlte::page')

@section('title', 'Master Status')

@section('content_header')
<h1>Master status</h1>
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
                    List Status
                    @can('status-create')
                    <a class="btn btn-success btn-sm" href="{{ route('status.create') }}"> Create New status</a>
                    @endcan
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-responsive table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <a class="btn btn-info btn-xs" href="{{ route('status.show',$user->id) }}">Show</a>
                                        @can('status-edit')
                                        <a class="btn btn-primary btn-xs" href="{{ route('status.edit',$user->id) }}">Edit</a>
                                        @endcan
                                        @can('status-delete')
                                        {!! Form::open(['method' => 'DELETE','route' => ['status.destroy', $user->id],'style'=>'display:inline']) !!}
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

        </div>
    </div>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable({});
    });
</script>
@stop