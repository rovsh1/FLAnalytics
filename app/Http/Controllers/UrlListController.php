<?php

namespace App\Http\Controllers;

use App\UrlList;
use Illuminate\Http\Request;

/**
 * Class UrlListController
 * @package App\Http\Controllers
 */
class UrlListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $urls = UrlList::all()->toArray();
        return view('url_list.index', compact('urls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('url_list.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = $this->validate(request(), [
            'name' => 'required',
            'url' => 'required|unique:url_lists',
        ]);
        $url['url'] = str_replace('http://', '', $url['url']);
        $url['url'] = str_replace('https://', '', $url['url']);
        $url['url'] = str_replace('www.', '', $url['url']);
        UrlList::create($url);

        return back()->with('success', 'Url has been added');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $urls = UrlList::find($id);
        return view('url_list.edit',compact('urls','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $url = UrlList::find($id);
        $this->validate(request(), [
            'name' => 'required',
            'url' => 'required|unique:url_lists',
        ]);
        $url->name = $request->get('name');
        $url->url = $request->get('url');
        $url->url = str_replace('http://', '', $url->url);
        $url->url = str_replace('https://', '', $url->url);
        $url->url = str_replace('www.', '', $url->url);
        $url->save();
        return redirect('url-list')->with('success','url has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $url = UrlList::find($id);
        $url->delete();
        return redirect('url-list')->with('success','url has been  deleted');
    }
}
