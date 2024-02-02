function displayRatingStars(avgRating) {
    let stars = '';
    if(avgRating < 1){
        stars = '<i class="bi bi-star-half"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>';
    }
    if(avgRating >= 1 && avgRating <1.5){
        stars = '<i class="bi bi-star-fill"></i>class="bi bi-star"></i>class="bi bi-star"></i>class="bi bi-star"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 1.5 && avgRating <2){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>class="bi bi-star"></i>class="bi bi-star"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 2 && avgRating <2.5){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>class="bi bi-star"></i>class="bi bi-star"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 2.5 && avgRating <3){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>class="bi bi-star"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 3 && avgRating <3.5){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>class="bi bi-star"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 3.5 && avgRating <4){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>class="bi bi-star"></i>';
    }
    if(avgRating >= 4 && avgRating <4.5){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>';
    }
    if(avgRating >= 4.5 && avgRating <4.8){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>';
    }
    if(avgRating >= 4.8){
        stars = '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>';
    }
    return stars;
}

// how to use
// let stars = displayRatingStars(row['avgRating']);
// $('.card-text.text-warning').html(stars);