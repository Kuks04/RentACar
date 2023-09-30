function Delete(value, text) {
    // text je promenljiva u slucaju sta brisemo auto/rezervacija
    if (confirm('Are you sure you want to delete this ' + text + ' from page?')) {
        // value zavisi koji auto/rezervacija
        document.getElementById("form"+value).submit();
    }
}

function Search() {
    var input, filter, cards, cardContainer, h5, title, i;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    cardContainer = document.getElementById("cars");
    cards = cardContainer.getElementsByClassName("card"); // Niz kartica sa automobilima
    for (i = 0; i < cards.length; i++) {
        title = cards[i].querySelector(".card-body h5.card-title");
        // Klasican search, uporedjujemo marke automobila sa tekstom iz search inputa
        if (title.innerText.toUpperCase().indexOf(filter) > -1) {  
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}