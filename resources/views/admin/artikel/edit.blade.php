@extends('adminlte::page')

@section('title', 'Master Artikel')

@section('content_header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Edit Artikel</h2>
        </div>
        @can('artikel-list')
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('artikel.index') }}"> Back</a>
        </div>
        @endcan
    </div>
</div>


@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


{!! Form::model($datacontent, ['method' => 'PATCH','route' => ['artikel.update', $datacontent->id]]) !!}
{{Form::hidden('departement_id',$datacontent->departement_id, array('id' => 'departement_id')) }}
{{Form::hidden('created_by',$datacontent->created_by, array('id' => 'created_by')) }}

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Title:</strong>
            {!! Form::text('name', $datacontent->name, array('placeholder' => 'Name','class' => 'form-control')) !!}
        </div>
        <div class="form-group">
            {{Form::label('your', 'Content')}}
            {!! Form::textarea('body',$datacontent->body,['class'=>'form-control', 'rows' => '2', 'cols' => '80','style'=>'height: 453px;']) !!}

        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('.permisionlist').select2();
        var options = {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
        };
        $('textarea').ckeditor(options);
    });
</script>
@stop