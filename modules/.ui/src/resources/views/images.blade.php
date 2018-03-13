@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">assignment</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Docker images</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Image</th>
                                <th>Size</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($images as $image)
                            <tr>
                                <td>{{ $image->id }}</td>
                                <td><label class="label label-success">{{ $image->tags[0] }}</label></td>
                                <td>{{ $image->size }}</td>
                                <td class="td-actions text-right">
                                    
                                    <a href=""  rel="tooltip" class="btn btn-info">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <button type="button" rel="tooltip" class="btn btn-danger " data-toggle="modal" data-target="#stopimage_{{ $image->id }}">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="" class="btn btn-info btn-round btn-fab btn-fab-mini">
                    <i class="material-icons">add</i>
                    <div class="ripple-image"></div>
                </a>
            </div>
        </div>
    </div>
</div>

@foreach($images as $image)
<div class="modal fade" id="startimage_{{ $image->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
            {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>
                    <h4 class="modal-title">Start image - {{ $image->tags[0] }}</h4>
                </div>
                <div class="modal-body">
                    <p>You sure?</p>
                    <input type="text" name="id" value="{{ $image->id }}" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Close<div class="ripple-image"><div class="ripple ripple-on ripple-out" style="left: 29.2812px; top: 19px; background-color: rgb(244, 67, 54); transform: scale(8.91015);"></div></div></button>
                    <button type="submit" class="btn btn-simple">Start</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
