<?php
    class SyncWrapper
    {
        function __construct(string $type)
        {
            $key = array(
                'routes' => new \App\scripts\RouteLoader(),
                'stops' => new \App\scripts\StopLoader(),
                'route_stops' => new \App\scripts\RouteStopLoader(),
            );
            foreach($key as $name => $loader)
            {
                if($name == $type || $type == 'all')
                {
                    $this->fast_sync($loader);
                }
            }
        }
        private function fast_sync($loader)
        {
            $syncer = new \App\scripts\Syncer($loader);
            $syncer->load();

            $syncer->check_diff();

            echo("\nTo be added:\n\n");
            foreach($syncer->to_add as $item)
            {
                echo($item->stringify() . "\n");
            }

            echo("\nTo be deleted:\n\n");
            foreach($syncer->to_delete as $item)
            {
                echo($item->stringify() . "\n");
            }

            echo("\nTo be added: "  .count($syncer->to_add) . "\n");
            echo("\nTo be deleted: " .count($syncer->to_delete) . "\n");

            $answer = readline("Delete shown? (y/n) ");
            $delete = $answer == "y";

            $answer = readline("Insert shown? (y/n) ");
            $insert = $answer == "y";

            $syncer->apply($delete, $insert);
        }
    }
?>