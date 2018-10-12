<?php

return [
    'rows_in_partition' => env('PARTITION_ROWS_IN_PARTITION', 10),
    'min_free_space_partition' => env('MIN_FREE_SPACE_IN_PARTITION', 5),
];