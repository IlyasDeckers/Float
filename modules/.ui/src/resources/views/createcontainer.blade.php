@extends('layouts.app')

@section('content')
<div class="col-md-6">
    <div class="card">
        <div class="card-header card-header-icon" data-background-color="rose">
            <i class="material-icons">contacts</i>
        </div>
        <div class="card-content">
            <h4 class="card-title">Create a new container</h4>
            <form method="POST" action="{{ route('createContainer') }}" class="form-horizontal">
            {{ csrf_field() }}
                <div class="row">
                    <label class="col-md-3 label-on-left">Domain</label>
                    <div class="col-md-9">
                        <div class="form-group label-floating is-empty">
                            <label class="control-label"></label>
                            <input name="domain" type="text" class="form-control" autocomplete="off">
                        <span class="material-input"></span></div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 label-on-left">Image</label>
                    <div class="col-md-9">
                        <div class="form-group label-floating is-empty">
                            <label class="control-label"></label>
                            <select name="image" class="form-control" autocomplete="off">
                                <option value="nginx">Nginx</option>
                                <option value="wordpress">WordPress</option>
                            </select>
                        <span class="material-input"></span></div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3"></label>
                    <div class="col-md-9">
                        <div class="form-group form-button pull-right">
                            <button type="submit" class="btn btn-fill btn-rose">Create</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


                <!-- <div class="row">
                    <label class="col-md-3 label-on-left">Enable PHP</label>
                    <label class="col-md-3"></label>
                    <div class="col-md-9">
                        <div class="checkbox form-horizontal-checkbox">
                            <label>
                                <input type="checkbox" name="optionsCheckboxes">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 label-on-left">PHP Version</label>
                    <div class="col-md-9">
                        <div class="form-group label-floating is-empty">
                            <label class="control-label"></label>
                            <select class="form-control" autocomplete="off">
                                <option>php5.6</option>
                                <option>php7.0</option>
                                <option>php7.1</option>
                            </select>
                        <span class="material-input"></span></div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 label-on-left">Database Username</label>
                    <div class="col-md-9">
                        <div class="form-group label-floating is-empty">
                            <label class="control-label"></label>
                            <input type="domain" class="form-control" autocomplete="off">
                        <span class="material-input"></span></div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 label-on-left">Database Password</label>
                    <div class="col-md-9">
                        <div class="form-group label-floating is-empty">
                            <label class="control-label"></label>
                            <input type="password" class="form-control" autocomplete="off">
                        <span class="material-input"></span></div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3"></label>
                    <div class="col-md-9">
                        <div class="checkbox form-horizontal-checkbox">
                            <label>
                                <input type="checkbox" name="optionsCheckboxes"> Remember Me
                            </label>
                        </div>
                    </div>
                </div> -->
@endsection
