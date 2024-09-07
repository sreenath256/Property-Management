@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Property: {{ $property->property_name }}</h1>
        <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="property_name">Property Name</label>
                <input type="text" class="form-control @error('property_name') is-invalid @enderror" id="property_name"
                    name="property_name" value="{{ old('property_name', $property->property_name) }}" maxlength="100">
                <div class="invalid-feedback" id="propertyNameError"></div>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                    name="phone" value="{{ old('phone', $property->phone) }}">
                <div class="invalid-feedback" id="phoneError"></div>

            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email', $property->email) }}" readonly>
                <div class="invalid-feedback" id="emailError"></div>

            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                    id="price" name="price" value="{{ old('price', $property->price) }}">
                <div class="invalid-feedback" id="priceError"></div>
            </div>

            <div class="form-group">
                <label for="currency">Currency</label>
                <select class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency"
                    required>
                    <option value="INR" {{ old('currency', $property->currency) == 'INR' ? 'selected' : '' }}>INR
                    </option>
                    <option value="AED" {{ old('currency', $property->currency) == 'AED' ? 'selected' : '' }}>AED
                    </option>
                </select>
                <div class="invalid-feedback" id="currencyError"></div>
            </div>

            <div class="form-group">
                <label for="facilities">Facilities</label>
                <div id="facilities-container">
                    @foreach (old('facilities', $property->facilities->pluck('facility_name')->toArray()) as $facility)
                        <div class="input-group mb-2 facility-group">
                            <input type="text" class="form-control" name="facilities[]" value="{{ $facility }}"
                                readonly required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-facility">-</button>
                            </div>
                        </div>
                    @endforeach
                    <div class="input-group mb-2 facility-group">
                        <input type="text" class="form-control">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success add-facility">+</button>
                        </div>
                    </div>
                </div>
                <div class="invalid-feedback" id="facilitiesError"></div>
            </div>

            <div class="form-group">
                <label>Current Images</label>
                <div class="row">
                    @foreach (json_decode($property->primary_image, true) as $image)
                        <div class="col-md-3 mb-3" id="image-{{ $image }}">
                            <img src="{{ Storage::url($image) }}" alt="Property Image" class="img-fluid">
                            <button type="button" class="btn btn-danger remove-image"
                                data-id="{{ $image }}">Remove</button>
                            <input type="hidden" name="existing_images[]" value="{{ $image }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group mt-4 mb-5">
                <label for="new_images ">Add New Images</label>
                <input type="file" class="form-control @error('new_images') is-invalid @enderror" id="new_images"
                    name="new_images[]" multiple>
                @error('new_images')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('new_images.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <input type="hidden" name="removed_images" id="removed_images">

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    rows="5" required>{{ old('description', $property->description) }}</textarea>
                <div class="invalid-feedback" id="descriptionError"></div>
            </div>

            <div class="form-group">
                <label for="inclusions_exclusions">Inclusions & Exclusions</label>
                <textarea class="form-control @error('inclusions_exclusions') is-invalid @enderror" id="inclusions_exclusions"
                    name="inclusions_exclusions" rows="5" required>{{ old('inclusions_exclusions', $property->inclusions_exclusions) }}</textarea>
                <div class="invalid-feedback" id="inclusionsExclusionsError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Update Property</button>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            CKEDITOR.replace('inclusions_exclusions');
            CKEDITOR.replace('description');

            $(document).ready(function() {


                $('form').submit(function(e) {
                    let isValid = true;

                    // Clear previous error messages
                    $('.invalid-feedback').remove();
                    $('.is-invalid').removeClass('is-invalid');

                    // Property Name Validation
                    let propertyName = $('#property_name').val().trim();
                    if (propertyName === '' || propertyName.length > 100) {
                        isValid = false;
                        $('#property_name').addClass('is-invalid');
                        $('#property_name').after(
                            '<div class="invalid-feedback">Property name is required and must be less than 100 characters.</div>'
                        );
                    }

                    // Phone Validation
                    let phone = $('#phone').val().trim();
                    if (phone === '' || isNaN(phone)) {
                        isValid = false;
                        $('#phone').addClass('is-invalid');
                        $('#phone').after(
                            '<div class="invalid-feedback">Phone number is required and must be numeric.</div>'
                        );
                    }

                    // Email Validation
                    let email = $('#email').val().trim();
                    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email === '' || !emailRegex.test(email)) {
                        isValid = false;
                        $('#email').addClass('is-invalid');
                        $('#email').after(
                            '<div class="invalid-feedback">Please enter a valid email address.</div>'
                        );
                    }

                    // Price Validation
                    let price = $('#price').val().trim();
                    if (price === '' || isNaN(price)) {
                        isValid = false;
                        $('#price').addClass('is-invalid');
                        $('#price').after(
                            '<div class="invalid-feedback">Price is required and must be numeric.</div>'
                        );
                    }

                    // Currency Validation
                    if ($('#currency').val() === null) {
                        isValid = false;
                        $('#currency').addClass('is-invalid');
                        $('#currency').after('<div class="invalid-feedback">Please select a currency.</div>');
                    }

                    // Facilities Validation
                    let facilities = $('input[name="facilities[]"]').map(function() {
                        return $(this).val().trim();
                    }).get();
                    if (facilities.length === 0 || facilities.some(f => f === '')) {
                        isValid = false;
                        $('#facilities-container').addClass('is-invalid');
                        $('#facilities-container').after(
                            '<div class="invalid-feedback">At least one facility is required.</div>'
                        );
                    }

                    // Description Validation
                    let description = CKEDITOR.instances.description.getData().trim();
                    if (description === '') {
                        isValid = false;
                        $('#cke_description').addClass('is-invalid');
                        $('#cke_description').after(
                            '<div class="invalid-feedback">Description is required.</div>'
                        );
                    }

                    // Inclusions & Exclusions Validation
                    let inclusionsExclusions = CKEDITOR.instances.inclusions_exclusions.getData().trim();
                    if (inclusionsExclusions === '') {
                        isValid = false;
                        $('#cke_inclusions_exclusions').addClass('is-invalid');
                        $('#cke_inclusions_exclusions').after(
                            '<div class="invalid-feedback">Inclusions & Exclusions are required.</div>'
                        );
                    }

                    // Prevent form submission if validation fails
                    if (!isValid) {
                        e.preventDefault();
                    }
                });


                // Add facility when '+' button is clicked
                $('.add-facility').on('click', function() {
                    let lastInputValue = $('#facilities-container .facility-group input:first').val();
                    if (lastInputValue != '') {
                        const container = $('#facilities-container');
                        const newFacilityGroup = `
                        <div class="input-group mb-2 facility-group">
                            <input type="text" class="form-control" name="facilities[]" value="${lastInputValue}" readonly required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-facility">-</button>
                            </div>
                        </div>
                        `;
                        container.append(newFacilityGroup);
                        $('#facilities-container .facility-group:first-child input').val('');
                    }
                });

                // Remove facility when '-' button is clicked
                $('#facilities-container').on('click', '.remove-facility', function() {
                    $(this).closest('.facility-group').remove();
                });
            });


            let removedImages = [];

            // Handle the remove button for existing images
            document.querySelectorAll('.remove-image').forEach(button => {
                button.addEventListener('click', function() {
                    let imageId = this.getAttribute('data-id');
                    alert(imageId)
                    removedImages.push(imageId);
                    document.getElementById('image-' + imageId).remove(); // Hide the image from UI
                    document.getElementById('removed_images').value = removedImages.join(
                        ','); // Update hidden input
                });
            });
        </script>
    @endpush
@endsection
