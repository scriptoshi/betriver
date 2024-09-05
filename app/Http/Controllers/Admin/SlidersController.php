<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Uploads;
use App\Http\Controllers\Controller;
use App\Http\Resources\Slider as SliderResource;
use App\Models\Slider;
use Inertia\Inertia;

use Illuminate\Http\Request;

class SlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Slider::query();
        if (!empty($keyword)) {
            $query->where('title', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('url', 'LIKE', "%$keyword%")
                ->orWhere('button', 'LIKE', "%$keyword%")
                ->orWhere('image', 'LIKE', "%$keyword%");
        }
        $slidersItems = $query->latest()->paginate($perPage);
        $sliders = SliderResource::collection($slidersItems);
        return Inertia::render('Admin/Sliders/Index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Admin/Sliders/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'url' => ['required', 'string'],
            'button' => ['required', 'string'],
            'image_uri' => ['required', 'string'],
            'active' => ['required', 'boolean'],
        ]);
        $slider = new Slider;
        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->url = $request->url;
        $slider->button = $request->button;
        $slider->active = $request->active;
        $slider->image = "";
        $slider->save();
        $upload = app(Uploads::class)->upload($request,  $slider, 'image');
        $slider->image =  $upload->url;
        $slider->save();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider added!');
    }



    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Slider $slider)
    {

        return Inertia::render('Admin/Sliders/Edit', [
            'slider' => new SliderResource($slider)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'url' => ['required', 'string'],
            'button' => ['required', 'string'],
            'image' => ['required', 'string'],
            'image_uri' => ['required_if:change_slider_image,true', 'string'],
            'change_slider_image' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],
        ]);

        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->url = $request->url;
        $slider->button = $request->button;
        $slider->image = $request->image;
        $slider->active = $request->active;
        $slider->save();
        if ($request->change_slider_image) {
            $upload = app(Uploads::class)->upload($request,  $slider, 'image');
            $slider->image =  $upload->url;
            $slider->save();
        }
        return back()->with('success', 'Slider updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Slider $slider)
    {
        $slider->active = !$slider->active;
        $slider->save();
        return back()->with('success', $slider->active ? __(' :name Slider Enabled !', ['name' => $slider->name]) : __(' :name  Slider Disabled!', ['name' => $slider->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Slider $slider)
    {
        $slider->delete();
        return back()->with('success', 'Slider deleted!');
    }
}
