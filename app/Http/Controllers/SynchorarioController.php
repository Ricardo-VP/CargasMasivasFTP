<?php

namespace App\Http\Controllers;

use App\Models\Synchorario;
use Illuminate\Http\Request;

/**
 * Class SynchorarioController
 * @package App\Http\Controllers
 */
class SynchorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $synchorarios = Synchorario::paginate();
        $synchorario = new Synchorario();

        return view('synchorario.index', compact('synchorarios', 'synchorario'))
            ->with('i', (request()->input('page', 1) - 1) * $synchorarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $synchorario = new Synchorario();
        return view('synchorario.create', compact('synchorario'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Synchorario::$rules);

        $synchorario = Synchorario::create($request->all());

        return redirect()->route('synchorarios.index')
            ->with('success', 'Synchorario created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $synchorario = Synchorario::find($id);

        return view('synchorario.show', compact('synchorario'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $synchorario = Synchorario::find($id);

        return view('synchorario.edit', compact('synchorario'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Synchorario $synchorario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Synchorario $synchorario)
    {
        request()->validate(Synchorario::$rules);

        $synchorario->update($request->all());

        return redirect()->route('synchorarios.index')
            ->with('success', 'Synchorario updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $synchorario = Synchorario::find($id)->delete();

        return redirect()->route('synchorarios.index')
            ->with('success', 'Synchorario deleted successfully');
    }
}
