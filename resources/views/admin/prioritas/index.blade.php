@extends('adminlte::page')

@section('title', 'Master Prioritas')

@section('content_header')
<h1>Master Prioritas</h1>
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
                    List Prioritas
                    @can('prioritas-create')
                    <a class="btn btn-success btn-sm" href="{{ route('prioritas.create') }}"> Create New Prioritas</a>
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
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <a class="btn btn-info btn-xs" href="{{ route('prioritas.show',$user->id) }}">Show</a>
                                        @can('prioritas-edit')
                                        <a class="btn btn-primary btn-xs" href="{{ route('prioritas.edit',$user->id) }}">Edit</a>
                                        @endcan
                                        @can('prioritas-delete')
                                        {!! Form::open(['method' => 'DELETE','route' => ['prioritas.destroy', $user->id],'style'=>'display:inline']) !!}
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