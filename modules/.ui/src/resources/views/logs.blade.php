@extends('layouts.app')

@section('content')


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Logs -
                    <small class="category" >View logs generated by Float</small>
                </h4>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="nav nav-pills nav-pills-icons nav-pills-rose nav-stacked" role="tablist">
                                                <!--
                                    color-classes: "nav-pills-primary", "nav-pills-info", "nav-pills-success", "nav-pills-warning","nav-pills-danger"
                                -->
                                <li class="active">
                                    <a href="#Float" role="tab" data-toggle="tab">
                                        <i class="material-icons">dashboard</i> Float
                                    </a>
                                </li>
                                <li>
                                    <a href="#Proxy" role="tab" data-toggle="tab">
                                        <i class="material-icons">schedule</i> Proxy
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content">
                                <div class="tab-pane active" id="Float">
                                    <pre>@foreach($logs as $log){{ $log }}<br>@endforeach</pre>
                                </div>
                                <div class="tab-pane" id="Proxy">
                                    Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas.
                                    <br />
                                    <br /> Dramatically maintain clicks-and-mortar solutions without functional solutions. Dramatically visualize customer directed convergence without revolutionary ROI. Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection