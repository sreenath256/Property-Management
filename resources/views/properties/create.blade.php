@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Property</h1>
        <form id="propertyForm" action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Property Name -->
            <div class="form-group">
                <label for="property_name">Property Name</label>
                <input type="text" class="form-control" id="property_name" name="property_name" maxlength="100">
                <div class="invalid-feedback" id="propertyNameError"></div>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone">
                <div class="invalid-feedback" id="phoneError"></div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email">
                <div class="invalid-feedback" id="emailError"></div>
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01">
                <div class="invalid-feedback" id="priceError"></div>
            </div>

            <!-- Currency -->
            <div class="form-group">
                <label for="currency">Currency</label>
                <select class="form-control" id="currency" name="currency">
                    <option value="" disabled>Select Currency</option>
                    <option value="INR">INR</option>
                    <option value="AED">AED</option>
                </select>
                <div class="invalid-feedback" id="currencyError"></div>
            </div>

            <!-- Facilities -->
            <div class="form-group">
                <label for="facilities">Facilities</label>
                <div id="facilities-container">
                    <div class="input-group mb-2 facility-group">
                        <input type="text" class="form-control" name="facilities[]" maxlength="255">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success add-facility">+</button>
                        </div>
                    </div>
                </div>
                <div class="invalid-feedback" id="facilitiesError"></div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                <div class="invalid-feedback" id="descriptionError"></div>
            </div>

            <!-- Inclusions & Exclusions -->
            <div class="form-group">
                <label for="inclusions_exclusions">Inclusions & Exclusions</label>
                <textarea class="form-control" id="inclusions_exclusions" name="inclusions_exclusions" rows="5" required></textarea>
                <div class="invalid-feedback" id="inclusionsExclusionsError"></div>
            </div>

            <!-- Images -->
            <div class="container mt-5">
                <div class="mb-3">
                    <label for="images" class="form-label">Images</label>
                    <input class="form-control" type="file" id="images" name="primary_image[]" multiple>
                    <small id="imageHelp" class="form-text text-muted">Allowed types: jpeg, png, jpg. Max size per image:
                        2MB.</small>
                    <div class="invalid-feedback" id="imageError"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Create Property</button>
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
        </script>
    @endpush
@endsection
