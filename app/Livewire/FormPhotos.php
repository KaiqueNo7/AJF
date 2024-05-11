<?php

namespace App\Livewire;

use App\Models\Photos;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormPhotos extends Component
{
    use WithFileUploads;

    public $propertyId;

    #[Validate(['photos.*' => 'image|max:1024'])]
    public $photos = [];

    public function save($id)
    {
        $property = Property::find($id);

        foreach ($this->photos as $photo) {
            Photos::create([
                'property_id' => $id,
                'photo' => $photo->store('property/' . strtolower($property->name), 'public'),
                'type' => 1,
            ]);   
        }
    }

    public function delete($id)
    {
        $photo = Photos::find($id);

        $image_path = $photo->photo;

        $image_exists = Storage::disk('public')->exists($image_path);
        
        if($image_exists){
            Storage::disk('public')->delete($image_path);
        }

        Photos::where('id', $id)->delete();
    }

    public function render()
    {
        $photosProperty = Photos::where('property_id', $this->propertyId)->get();

        return view('livewire.form-photos', ['photosProperty' => $photosProperty]);
    }
}
