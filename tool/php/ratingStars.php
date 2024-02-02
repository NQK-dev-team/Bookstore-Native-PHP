<?php
function displayRatingStars($avgRating) {
    if($avgRating <1){
        echo '<i class="bi bi-star-half"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 1 && $avgRating <1.5){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 1.5 && $avgRating <2){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-half"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 2 && $avgRating <2.5){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 2.5 && $avgRating <3){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-half"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 3 && $avgRating <3.5){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 3.5 && $avgRating <4){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-half"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 4 && $avgRating <4.5){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star"></i>';
  }
  elseif($avgRating >= 4.5 && $avgRating <4.8){
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-fill"></i>';
        echo '<i class="bi bi-star-half"></i>';
  }
  elseif($avgRating >= 4.8){
      echo '<i class="bi bi-star-fill"></i>';
      echo '<i class="bi bi-star-fill"></i>';
      echo '<i class="bi bi-star-fill"></i>';
      echo '<i class="bi bi-star-fill"></i>';
      echo '<i class="bi bi-star-fill"></i>';
  }
}
?>