<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::get();
        return view('home', compact('properties'));
    }

    public function viewCreateForm()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());

        // return $request;
        $validateData = $request->validate([
            'property_name' => 'required|string|max:100',
            'phone' => 'required|numeric',
            'email' => 'required|email|unique:properties',
            'price' => 'required|numeric',
            'currency' => 'required',
            'facilities' => 'required|array|min:1',
            'facilities.*' => 'required|string|max:255',
            'description' => 'required',
            'inclusions_exclusions' => 'required',
            // 'primary_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'primary_image' => 'required',
        ]);

        $validateData['property_slug'] = Str::slug($validateData['property_name']);
        $validateData['user_id']=auth()->user()->id;

        if ($request->hasFile('primary_image')) {
            $imagePaths = [];
            foreach ($request->file('primary_image') as $image) {
                $filename = time() . '-' . $image->getClientOriginalName();
                $path = $image->storeAs('public/properties', $filename);
                $imagePaths[] = $path;
            }
            $validateData['primary_image'] = json_encode($imagePaths);
        }

        $property = Property::create($validateData);
        $facility = $validateData['facilities'];
        foreach ($request->input('facilities') as $facility) {
            PropertyFacility::create([
                'property_id' => $property->id,
                'facility_name' => $facility,
                'status' => 1,
            ]);
        }


        return redirect()->route('home')->with('success', 'Property added successfully.');
    }

    public function edit(Property $property)
    {
        $images = json_decode($property->primary_image, true);
        return view('properties.edit', compact('property', 'images'));
    }

    public function show(Property $property)
    {
        // return $property->facilities;
        $images = json_decode($property->primary_image, true);
        return view('properties.show', compact('property', 'images'));
    }

    public function update(Request $request, Property $property)
    {
        // dd($request->all()); // For debugging

        $validateData = $request->validate([
            'property_name' => 'required|string|max:100',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'price' => 'required|numeric',
            'currency' => 'required',
            'facilities' => 'required|array',
            'facilities.*' => 'required|string|max:255',
            'description' => 'required',
            'inclusions_exclusions' => 'required',
        ]);

        $validateData['property_slug'] = Str::slug($request['property_name']);


        $existingImages = json_decode($property->primary_image, true) ?: [];
        // dd($existingImages);
        // Handle removed images
        if ($request->has('removed_images')) {
            $removedImages = explode(',', $request->removed_images);
            foreach ($removedImages as $imagePath) {
                if (($key = array_search($imagePath, $existingImages)) !== false) {
                    // Delete the file from storage
                    Storage::delete($imagePath);
                    // Remove from the list of existing images
                    unset($existingImages[$key]);
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $filename = time() . '-' . $image->getClientOriginalName();
                $path = $image->storeAs('public/properties', $filename);
                $existingImages[] = $path; // Append new image to the list
            }
        }

        // Re-encode the image array and store it
        $validateData['primary_image'] = json_encode(array_values($existingImages)); // Re-index array


        // Re-encode the image array and store it
        $validateData['primary_image'] = json_encode(array_values($existingImages)); // Re-index array

        // Update the property
        $property->update($validateData);

        // Update property fields
        $property->update($request->all());

        // Handle property facilities
        // Existing facilities
        $existingFacilities = $property->facilities()->pluck('facility_name')->toArray();

        // Create new facilities
        foreach ($request->input('facilities') as $facility) {
            if (!in_array($facility, $existingFacilities)) {
                PropertyFacility::create([
                    'property_id' => $property->id,
                    'facility_name' => $facility,
                    'status' => 1,
                ]);
            }
        }

        // Remove facilities that are no longer present
        foreach ($existingFacilities as $existingFacility) {
            if (!in_array($existingFacility, $request->input('facilities'))) {
                $facilityToRemove = PropertyFacility::where('property_id', $property->id)
                    ->where('facility_name', $existingFacility)
                    ->first();
                if ($facilityToRemove) {
                    $facilityToRemove->delete();
                }
            }
        }

        session()->flash('success', $property->property_name . ' updated successfully!');
        return redirect()->route('home');
    }

    public function destroy(Property $property)
    {

        $property->delete();
        session()->flash('success', 'Task  Deleted!');


        return redirect()->route('home')->with('success', 'Task deleted successfully.');
    }
}
