@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Properties</h1>
        <a href="{{ route('properties.create') }}" class="btn btn-primary mb-3">Add New Property</a>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Price</th>
                    <th>Currency</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                    <tr>
                        <td>{{ $property->id }}</td>
                        <td>{{ $property->property_name }}</td>
                        <td>{{ $property->phone }}</td>
                        <td>{{ $property->price }}</td>
                        <td>{{ $property->currency }}</td>
                        <td class="align-middle">
                            <div style="width: 100px; height: 100px; overflow: hidden;">
                                <img src="{{ Storage::url(json_decode($property->primary_image, true)[0]) }}"
                                    alt="Property Image" class="img-fluid rounded"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('properties.show', $property->id) }}" class="btn btn-sm btn-info">View</a>

                            @if (auth()->user()->id == $property->user_id)
                                <a href="{{ route('properties.edit', $property->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('properties.destroy', $property->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
