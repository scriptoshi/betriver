<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Feedback as FeedbackResource;
use App\Models\Feedback;
use Inertia\Inertia;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Feedback::query()->with(['user']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('feedback', 'LIKE', "%$keyword%");
        }
        $feedbackItems = $query->latest()->paginate($perPage);
        $feedback = FeedbackResource::collection($feedbackItems);
        return Inertia::render('Admin/Feedback/Index', compact('feedback'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('feedback.index')->with('success', 'Feedback deleted!');
    }
}
