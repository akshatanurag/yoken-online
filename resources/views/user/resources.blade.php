@extends('user.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h5>Course Resources</h5>
        </div>
        <div class="row">
            <div class="col m12">
                <table>
                    <thead>
                        <th>Resource Name</th>
                        <th>Course Name</th>
                        <th>Expiry Date</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach($resources['courses'] as $resource)
                        <tr>
                            <td>{{$resource->name}}</td>
                            <td>{{$resource->course->name}}</td>
                            @if($resource->expiry)
                                <td>{{$resource->expiry}}</td>
                            @else
                                <td>No expiry</td>
                            @endif
                            <td>
                                <a href="{{route('user.resources-view', $resource->id)}}">View</a>
                            </td>
                        </tr>
                        @endforeach
                        @if($resources['courses']->count() <= 0)
                        <tr>
                            <td>No resources available</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row center-align">
            {{$resources['courses']->links()}}
        </div>
        <br />
        <br />
        <hr />
        <br />
        <br />
        <br />
        <div class="row">
            <h5>Webinar Resources</h5>
        </div>
        <div class="row">
            <div class="col m12">
                <table>
                    <thead>
                        <th>Resource Name</th>
                        <th>Webinar Name</th>
                        <th>Expiry Date</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach($resources['webinars'] as $resource)
                        <tr>
                            <td>{{$resource->name}}</td>
                            <td>{{$resource->webinar->name}}</td>
                            @if($resource->expiry)
                                <td>{{$resource->expiry}}</td>
                            @else
                                <td>No expiry</td>
                            @endif
                            <td>
                                <a href="{{route('user.resources-view', $resource->id)}}">View</a>
                            </td>
                        </tr>
                        @endforeach
                        @if($resources['webinars']->count() <= 0)
                        <tr>
                            <td>No resources available</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row center-align">
            <ul class="pagination" id="page-numbers">
                {{$resources['webinars']->links()}}
            </ul>
        </div>
    </div>
@endsection

@section('footer')
@endsection
