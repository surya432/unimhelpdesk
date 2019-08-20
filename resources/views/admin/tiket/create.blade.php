@extends('adminlte::page')

@section('title', 'Create Tiket')

@section('content_header')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Create Tiket</h2>
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


{!! Form::open(array('route' => 'tiket.store','method'=>'POST','files'=> true)) !!}
{{Form::hidden('senders', Auth::user()->name, array('id' => 'senders')) }}
@if (Auth::user()->hasRole("User"))
{{Form::hidden('repply', 0, array('id' => 'repply')) }}
@else
{{Form::hidden('repply', 1, array('id' => 'repply')) }}
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="form-group">
                        {{Form::label('subject', 'Subject')}}
                        {!! Form::text('subject', null, array('placeholder' => 'Subject','class' =>'form-control' )) !!}
                    </div>
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
        <div class="col-xs-4 col-sm-4 col-md-4 text-center">
            @hasrole('User')
            {{Form::hidden('user_id', Auth::user()->id, array('id' => 'user_id')) }}
            @else
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('user', 'User')}}

                    </div>

                    <div class="panel-body">
                        <select name="user_id" id="user_id" class="permisionlist form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1">
                            @foreach($user as $value)
                            <option value="{{$value->id}}">{{ $value->name}}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>
            @endhasrole

            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Prioritas', 'Prioritas')}}
                    </div>
                    <div class="panel-body">
                        <select name="prioritas_id" id="prioritas_id" class=" form-control " style="width: 100%;" tabindex="-1">
                            @foreach($prioritas as $value)
                            <option value="{{$value->id}}" {{ ($value->name == "Medium") ? "selected" : ""}}>{{ $value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Service', 'Service')}}
                    </div>
                    <div class="panel-body">
                        <select name="services_id" id="services_id" class="permisionlist form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1">
                            @foreach($services as $value)
                            <option value="{{$value->id}}" {{ ($value->name == "Medium") ? "selected" : ""}}>{{ $value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Status', 'Status')}}
                    </div>
                    <div class="panel-body">
                        <select name="status_id" id="status_id" class=" form-control " style="width: 100%;" tabindex="-1">
                            @foreach($status as $value)
                            <option value="{{$value->id}}" {{ ($value->name == "Open") ? "selected" : ""}}>{{ $value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        {{Form::label('Departement', 'Departement')}}
                    </div>
                    <div class="panel-body">
                        <select name="departement_id" id="departement_id" class=" form-control " style="width: 100%;" tabindex="-1">
                            @foreach($departement as $value)
                            <option value="{{$value->id}}">{{ $value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        </div>
    </div>
</div>
{!! Form::close() !!}
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('.permisionlist').select2();
        var options = {
            baseHref: "{{ url('/') }}",
            path_absolute: "/",
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
        };
        var LFMButton = function(context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="note-icon-picture"></i> ',
                tooltip: 'Insert image with filemanager',
                container: false,
                click: function() {

                    lfm({
                        type: 'image',
                        prefix: '/filemanager'
                    }, function(url, path) {
                        context.invoke('insertImage', url);
                    });

                }
            });
            return button.render();
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