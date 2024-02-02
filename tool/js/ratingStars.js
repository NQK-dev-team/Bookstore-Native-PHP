function displayRatingStars(avgRating)
{
    if (avgRating < 0.5)
    {
        return ' <i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 0.5 && avgRating < 1)
    {
        return ' <i class="bi bi-star-half"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 1 && avgRating < 1.5)
    {
        return ' <i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 1.5 && avgRating < 2)
    {
        return ' <i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-half"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 2 && avgRating < 2.5)
    {
        return ' <i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 2.5 && avgRating < 3)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-half"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 3 && avgRating < 3.5)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 3.5 && avgRating < 4)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-half"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 4 && avgRating < 4.5)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star"></i>';
    } else if (avgRating >= 4.5 && avgRating < 4.8)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-half"></i>';
    } else if (avgRating >= 4.8)
    {
        return '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>' +
            '<i class="bi bi-star-fill"></i>';
    }
}

// Example usage:
// var rating = 3.7;
// var starsHtml = displayRatingStars(rating);
// console.log(starsHtml);


// how to use
// let stars = displayRatingStars(row['avgRating']);
// $('.card-text.text-warning').html(stars);