@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">assignment</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Docker images test</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Last Modified</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vhosts as $vhost)
                            <tr>
                                <td>{{ $vhost->name }}</td>
                                <td>{{ $vhost->path . $vhost->name }}</td>
                                <td>{{ $vhost->modified_on }}</td>
                                <td class="td-actions text-right">
                                    
                                    <a href=""  rel="tooltip" class="btn btn-info">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <button type="button" rel="tooltip" class="btn btn-danger " data-toggle="modal" data-target="">
                                        <i class="material-icons">delete</i>
                                    </button>
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
@endsection
