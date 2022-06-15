<?php

namespace App\Repositories;

use App\Models\{Series, Season, Episode};
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SeriesFormRequest;

class EloquentSeriesRepository implements SeriesRepository
{

    public function add(SeriesFormRequest $request) : Series
    {
        return $serie = DB::transaction(function () use ($request, &$serie) {
            $serie = Series::create($request->all());
            $seasons = [];
            for($i = 1; $i <= $request->seasonsQty ; $i++){
                $seasons[] = [
                    'series_id' => $serie->id,
                    'number' => $i,
                ];
            }
            Season::insert($seasons);

            $episodes = [];
            foreach ($serie->seasons as $season){
                for($j = 1; $j <= $request->episodesPerSeason ; $j++){
                    $episodes[] = [
                        'season_id' => $season->id,
                        'number' => $j
                    ];
                }
            }
            Episode::insert($episodes);

            return $serie;
        });
    }
}