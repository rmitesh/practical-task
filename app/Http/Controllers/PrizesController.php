<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrizeRequest;
use App\Models\Prize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $prizes = cache()->remember('prizes', now()->addDays(1), function() {
            $prizes = Prize::select([
                'id', 'title', 'probability', 'awarded',
            ])->get();
            $awarded = $prizes->pluck('awarded')->sum();
            $prizes->each(function (&$prize) use ($awarded) {
                // if ( $awarded ) {
                // }
                $awardedProbability = round($awarded ? ($prize->awarded / $awarded) * 100 : 0, 2);

                $prize->awardedLabel = "{$prize->title} ({$awardedProbability}%)";
                $prize->awardedProbability = $awardedProbability;
                $prize->probabilityLabel = "{$prize->title} ({$prize->probability}%)";
                $prize->probabilityColors = Prize::rndRGBColorCode();
                $prize->rewardColors = Prize::rndRGBColorCode();
            });

            return $prizes;
        });


        return view('prizes.index', [
            'prizes' => $prizes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request): RedirectResponse
    {
        Prize::create($request->validated());

        cache()->forget('prizes');
        cache()->forget('totalProbability');

        return to_route('prizes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Prize $prize): View
    {
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, Prize $prize): RedirectResponse
    {
        $prize->update($request->validated());

        cache()->forget('prizes');
        cache()->forget('totalProbability');

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        cache()->forget('prizes');
        cache()->forget('totalProbability');

        return to_route('prizes.index');
    }


    public function simulate(Request $request): RedirectResponse
    {
        for ($i = 0; $i < $request->number_of_prizes ?? 10; $i++) {
            Prize::nextPrize();
        }

        cache()->forget('prizes');

        return to_route('prizes.index');
    }

    public function reset(): RedirectResponse
    {
        Prize::query()->update([
            'awarded' => 0,
        ]);

        cache()->forget('prizes');
        return to_route('prizes.index');
    }
}
