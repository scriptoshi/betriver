<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Setting as SettingResource ;
use App\Models\Setting;
use Inertia\Inertia;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Setting::query();
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
			->orWhere('val', 'LIKE', "%$keyword%");
        } 
        $settingsItems = $query->latest()->paginate($perPage);
        $settings = SettingResource::collection($settingsItems);
        return Inertia::render('AdminSettings/Index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminSettings/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'name' => ['required','string'],
			'val' => ['required','string'],
		]);
        $setting = new Setting;
        $setting->name = $request->name;
		$setting->val = $request->val;
		$setting->save();
        
        return redirect()->route('settings.index')->with('success', 'Setting added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Setting $setting)
    {
        
        return Inertia::render('AdminSettings/Show', [
            'setting'=> new SettingResource($setting)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Setting $setting)
    {
        
        return Inertia::render('AdminSettings/Edit', [
            'setting'=> new SettingResource($setting)
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
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
			'name' => ['required','string'],
			'val' => ['required','string'],
		]);
        
        $setting->name = $request->name;
		$setting->val = $request->val;
		$setting->save();
        return back()->with('success', 'Setting updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Setting $setting)
    {
        $setting->active = !$setting->active;
        $setting->save();
        return back()->with('success', $setting->active ? __(' :name Setting Enabled !', ['name' => $setting->name]) : __(' :name  Setting Disabled!', ['name' => $setting->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Setting $setting)
    {
        $setting->delete();
        return redirect()->route('settings.index')->with('success', 'Setting deleted!');
    }
}
