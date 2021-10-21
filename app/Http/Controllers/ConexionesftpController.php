<?php

namespace App\Http\Controllers;

use App\Models\Conexionesftp;
use Illuminate\Http\Request;

/**
 * Class ConexionesftpController
 * @package App\Http\Controllers
 */
class ConexionesftpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conexionesftps = Conexionesftp::paginate();

        return view('conexionesftp.index', compact('conexionesftps'))
            ->with('i', (request()->input('page', 1) - 1) * $conexionesftps->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $conexionesftp = new Conexionesftp();
        return view('conexionesftp.create', compact('conexionesftp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Conexionesftp::$rules);

        $conexionesftp = Conexionesftp::create($request->all());

        return redirect()->route('conexionesftps.index')
            ->with('success', 'Conexionesftp created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conexionesftp = Conexionesftp::find($id);

        return view('conexionesftp.show', compact('conexionesftp'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $conexionesftp = Conexionesftp::find($id);

        return view('conexionesftp.edit', compact('conexionesftp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Conexionesftp $conexionesftp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conexionesftp $conexionesftp)
    {
        request()->validate(Conexionesftp::$rules);

        $conexionesftp->update($request->all());

        return redirect()->route('conexionesftps.index')
            ->with('success', 'Conexionesftp updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $conexionesftp = Conexionesftp::find($id)->delete();

        return redirect()->route('conexionesftps.index')
            ->with('success', 'Conexionesftp deleted successfully');
    }
}
