@extends('adminlte::page')

@section('title', 'Tiket #'.$tiket->id)

@section('content_header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{$tiket->subject}}</h2>
        </div>
        @can('tiket-list')
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('tiket.index') }}"> Back</a>
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


@if(Session::has('success'))
<div class="alert alert-success">
    {{ Session::get('success')}}
</div>
@endif

<div class="row">

    <div class="col-lg-12">
        <div class="col-xs-4 col-sm-4 col-md-4">
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Prioritas', 'Prioritas')}}
                    </div>
                    <div class="panel-body">
                        {{$tiket->prioritasName}}
                    </div>
                </div>
            </div>


        </div>

        <div class="col-xs-4 col-sm-4 col-md-4">
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Departement', 'Departement')}}
                    </div>
                    <div class="panel-body">
                        <div class="control-group input-group">
                            {{$tiket->departementName}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="control-group input-group">
                            {{Form::label('Status', 'Status')}}
                        </div>
                    </div>
                    <div class="panel-body">
                        {!! Form::model($tiket, ['method' => 'PATCH','route' => ['tiket.update', $tiket->id]]) !!}
                        <div class="control-group input-group">
                            <select name="status_id" id="status_id" class=" form-control " style="width: 100%;" tabindex="-1">
                                @foreach($status as $value)
                                <option value="{{$value->id}}" {{ ($tiket->statusName == $value->name) ? "selected" : ""}}>{{ $value->name}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>



                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::open(array('route' => 'tiket.replyTiket','method'=>'POST','files'=> true)) !!}
        {{Form::hidden('senders', Auth::user()->name, array('id' => 'senders')) }}
        {{Form::hidden('repply', 1, array('id' => 'repply')) }}
        {{Form::hidden('tiket_id', $tiket->id, array('id' => 'tiket_id')) }}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Balasan
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {{Form::label('your', 'Pesan')}}
                        {!! Form::textarea('body',null,['class'=>'form-control', 'rows' => '2', 'cols' => '80','style'=>'height: 453px;']) !!}

                    </div>
                    <div class="form-group">

                        {{Form::label('File Berkas', 'File Berkas')}}
                        <div class="input-group control-group increment">
                            <input type="file" name="attachment[]" class="form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                            </div>
                        </div>
                        <div class="clone hide">
                            <div class="control-group input-group" style="margin-top:10px">
                                <input type="file" name="attachment[]" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                </div>
            </div>

        </div>
        {!! Form::close() !!}

    </div>
    <div class="col-lg-12">

    </div>
</div>
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
        $(".btn-success").click(function() {
            var html = $(".clone").html();
            $(".increment").after(html);
        });

        $("body").on("click", ".btn-danger", function() {
            $(this).parents(".control-group").remove();
        });
    });
</script>
@stop