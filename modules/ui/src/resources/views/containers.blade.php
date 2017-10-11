@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">assignment</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Docker Containers</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>State</th>
                                <th>Status</th>
                                <th>Ports</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($containers as $container)
                            <tr>
                                <td>{{ preg_replace('/\//', '', $container->names[0]) }}</td>
                                <td>{{ $container->image }}</td>
                                <td><label class="label @if($container->state == "running") label-success @else label-danger @endif">{{ $container->state }}</label></td>
                                <td>{{ $container->status }}</td>
                                <td>
                                    @foreach($container->ports as $port)
                                        0.0.0.0:{{ $port->publicPort . ' -> ' . $port->privatePort }}
                                    @endforeach
                                </td>
                                <td class="td-actions text-right">
                                    <button type="button" rel="tooltip" class="btn btn-success @if($container->state == "running") disabled @endif" data-toggle="modal" data-target="#startContainer_{{ $container->id }}">
                                        <i class="material-icons">play_arrow</i>
                                    </button>
                                    <button type="button" rel="tooltip" class="btn btn-warning @if($container->state != "running") disabled @endif" data-toggle="modal" data-target="#pauseContainer_{{ $container->id }}">
                                        <i class="material-icons">pause</i>
                                    </button>
                                    <button type="button" rel="tooltip" class="btn btn-danger @if($container->state != "running") disabled @endif" data-toggle="modal" data-target="#stopContainer_{{ $container->id }}">
                                        <i class="material-icons">stop</i>
                                    </button>
                                    <a href="{{ route('container', [ 'id' => $container->id ]) }}"  rel="tooltip" class="btn btn-info">
                                        <i class="material-icons">edit</i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('createContainerPage') }}" class="btn btn-info btn-round btn-fab btn-fab-mini">
                    <i class="material-icons">add</i>
                    <div class="ripple-container"></div>
                </a>
            </div>
        </div>
    </div>
</div>

@foreach($containers as $container)
<div class="modal fade" id="startContainer_{{ $container->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('startContainer') }}">
            {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>
                    <h4 class="modal-title">Start Container - {{ $container->names[0] }}</h4>
                </div>
                <div class="modal-body">
                    <p>You sure?</p>
                    <input type="text" name="id" value="{{ $container->id }}" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Close<div class="ripple-container"><div class="ripple ripple-on ripple-out" style="left: 29.2812px; top: 19px; background-color: rgb(244, 67, 54); transform: scale(8.91015);"></div></div></button>
                    <button type="submit" class="btn btn-simple">Start</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pauseContainer_{{ $container->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pauseContainer') }}">
            {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>
                    <h4 class="modal-title">Pause Container - {{ $container->names[0] }}</h4>
                </div>
                <div class="modal-body">
                    <p>You sure?</p>
                    <input type="text" name="id" value="{{ $container->id }}" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Close<div class="ripple-container"><div class="ripple ripple-on ripple-out" style="left: 29.2812px; top: 19px; background-color: rgb(244, 67, 54); transform: scale(8.91015);"></div></div></button>
                    <button type="submit" class="btn btn-simple">Stop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="stopContainer_{{ $container->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('stopContainer') }}">
            {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>
                    <h4 class="modal-title">Stop Container - {{ $container->names[0] }}</h4>
                </div>
                <div class="modal-body">
                    <p>You sure?</p>
                    <input type="text" name="id" value="{{ $container->id }}" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Close<div class="ripple-container"><div class="ripple ripple-on ripple-out" style="left: 29.2812px; top: 19px; background-color: rgb(244, 67, 54); transform: scale(8.91015);"></div></div></button>
                    <button type="submit" class="btn btn-simple">Stop</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
