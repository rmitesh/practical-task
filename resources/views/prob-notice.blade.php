<?php

use App\Models\Prize;

// add into the cache
$current_probability = Prize::getTotalProbability();
$remaining_probability = Prize::MAX_PROBABILITY_LIMIT - $current_probability;

?>

@if( $current_probability < Prize::MAX_PROBABILITY_LIMIT )
	<div class="alert alert-danger" role="alert">
	  	<span>Sum of all prizes probability must be {{ Prize::MAX_PROBABILITY_LIMIT }}%. Currently its {{ $current_probability }}% You have yet to add {{ $remaining_probability }}% to the prize.</span>
	</div>
@endif
