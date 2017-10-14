@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">assignment</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Nginx configuration for {{ $vhost[0]->name }}</h4>
                <div class="table-responsive">
                    <pre>
                        {{ $vhost[0]->file_contents }}
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection