@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">assignment</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Proxy configuration for {{ $vhost[0]->name }}</h4>
                <div class="table-responsive">
                    <div class="form-group label-floating is-empty">
                        <!-- <label class="control-label">Email address</label> -->
                        <textarea  rows="25" cols="50" class="form-control" autocomplete="off" style="cursor: auto;">
                            {{ $vhost[0]->file_contents }}
                        </textarea>
                    <span class="material-input"></span></div>
                </div>
                <div class="form-footer text-right">
                    <button type="submit" class="btn btn-rose btn-fill">Update configuration<div class="ripple-container"></div></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection