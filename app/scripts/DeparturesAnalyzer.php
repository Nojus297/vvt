<?php

namespace App\scripts;

use DateTime;

class DeparturesAnalyzer
{
    // private $departures;
    public $departures;

    public function analyze($result)
    {
        $this->load_expected_times($result->started_at, $result->finished_at);

        foreach($this->departures as $departure)
        {
            if(!array_key_exists($departure->id, $result->departures))
            {
                continue;
            }
            $this->analyze_departures(
                $departure->id, $result->departures[$departure->id]
            );
        }
    }

    private function analyze_departures($rs_id, $actual_times)
    {
        for($i = 0; $i < count($this->departures[$rs_id]); $i++)
        {
            if($i = count($actual_times))
            {
                break;
            }
            $this->departures[$i]->actual_time = $actual_times[$i]->time;
        }
    }

    public function __construct()
    {
        $this->load_expected_times(new DateTime('2019-09-16 16:00:00'), new DateTime('2019-09-16 18:00:00'));
        return $this->departures;
    }

    private function load_expected_times($from, $to)
    {
        foreach($this->get_route_stops() as $i => $route_stop)
        {
            info("{$i} / 835?");
            $this->departures[$route_stop->id] = \DB::table('departures')
                ->whereDate('date', $from->format('Y-m-d'))
                ->where('route_stop_id', $route_stop->id)
                ->whereBetween('expected_time', [
                    $from->format('H:i:s'),
                    $to->format('H:i:s'),
                ])
                ->get();
        }
    }

    private function get_route_stops()
    {
        return \DB::table('route_stops')->get();
    }
}

?>