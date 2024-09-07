@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-body">
            <h1 class="card-title h2 mb-4">{{ $property->property_name }}</h1>

            <!-- Image Gallery moved to the top -->
            <h3 class="h4 mb-3">Images</h3>
            <div class="row mb-4">
                @foreach ($images as $image)
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="ratio ratio-1x1">
                            <img src="{{ Storage::url($image) }}" alt="Property Image" class="img-fluid rounded object-fit-cover">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Slug:</strong> {{ $property->property_slug }}</p>
                    <p><strong>Phone:</strong> {{ $property->phone }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> {{ $property->email }}</p>
                    <p><strong>Price:</strong> {{ $property->currency }} {{ number_format($property->price, 2) }}</p>
                </div>
            </div>

            <h3 class="h4 mb-3">Facilities</h3>
            <ul class="list-group list-group-flush mb-4">
                @foreach ($property->facilities as $facility)
                    <li class="list-group-item">{{ $facility->facility_name }}</li>
                @endforeach
            </ul>

            <h3 class="h4 mb-3">Description</h3>
            <div class="mb-4">{!! $property->description !!}</div>

            <h3 class="h4 mb-3">Inclusions & Exclusions</h3>
            <div class="mb-4">{!! $property->inclusions_exclusions !!}</div>

            @if (auth()->user()->id == $property->user_id)
                <div class="mt-4">
                    <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-warning me-2">Edit Property</a>
                    <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete Property</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endpush
