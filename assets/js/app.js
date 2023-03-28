function updateCookie(name, value) {
    let date = new Date(Date.now() + 86400000*30); //86400000ms = 1 jour
    date = date.toUTCString();

    //Mettre à jour le cookie
    document.cookie = name+'='+value+'; path=/; expires=' + date; 
}

function getCookie(nomCookie) {
    deb = document.cookie.indexOf(nomCookie+ "=")
    if (deb >= 0) {
        deb += nomCookie.length + 1
        fin = document.cookie.indexOf(";",deb)
        if (fin < 0) fin = document.cookie.length
        return unescape(document.cookie.substring(deb,fin))
    } else {
        return ""
    }
}

function addCardShopping(article) {
    //convertir mon id en string
    article = article.toString();

    //récupère le cookie au format |3|4
    var actualCard = getCookie("cardShopping");

    //transforme en tableau le cookie
    actualCard = actualCard.split('|');

    //Ajoute ma valeur dans le tableau
    actualCard.push(article);

    // Vérifier les doublons
    actualCard = actualCard.filter((x, i) => actualCard.indexOf(x) === i);

    //créer une variable vide pour actualisé le cookie
    var cardShopping = "";
    //Fabrique le format spécial
    for(i=0; i<actualCard.length; i++) {
        if(actualCard[i] != "" && actualCard[i] != "[]") {
            cardShopping += '|'+actualCard[i];
        }
    }

    //Mettre à jour la panier
    updateCookie("cardShopping", cardShopping);

    //Mie à jour du badge pour l'article
    document.getElementById("article-"+article).classList.remove('fa-cart-plus');
    document.getElementById("article-"+article).classList.add('fa-badge-check', 'text-success');

    //Mettre à jour l'icon du panier
    document.getElementById('notifCardShopping').innerHTML = actualCard.length-1;
}


// Using fetch
function downloadImage(idImage,$name) {
    var link = document.createElement("a");
    link.download = $name;
    link.href = window.location.origin+idImage;
    link.click();
}

// Mettre la limite pour les crédits
function creditEgalEuroForFormAddPhoto(frais) {
    valueCredit = document.getElementById('creditPricePhoto').value;

    if(valueCredit >= 2) {
        if(valueCredit <= 100) {
            if(valueCredit > 0) {
                calcul = parseFloat(valueCredit)*5
                calculFrais = calcul-(calcul*frais/100);
                document.getElementById('creditEgalEuroForFormAddPhoto').innerHTML = "≈ "+ calcul.toFixed(2) +" € pour le client (charge -"+ frais +" % soit "+ calculFrais.toFixed(2) +" € pour vous)";
            } else {
                document.getElementById('creditEgalEuroForFormAddPhoto').innerHTML = "";
            }
        } else {
            document.getElementById('creditPricePhoto').value = 100;
        }
    } else {
        document.getElementById('creditPricePhoto').value = "";
    }
}

// Limite pour la description
function descriptionForFormAddPhoto() {
    limit = 200;
    longueur = document.getElementById('descriptionPhoto').value.length;
    texte = document.getElementById('descriptionPhoto').value;

    if(longueur > limit) {
        document.getElementById('descriptionPhoto').value = texte.substring(0, limit);
    }
    document.getElementById('descriptionForFormAddPhoto').innerHTML = longueur+" / "+limit;
}


// Limite pour le titre
function titleForFormAddPhoto() {
    limit = 40;
    longueur = document.getElementById('titlePhoto').value.length;
    texte = document.getElementById('titlePhoto').value;

    if(longueur > limit) {
        document.getElementById('titlePhoto').value = texte.substring(0, limit);
    }
    document.getElementById('titleForFormAddPhoto').innerHTML = longueur+" / "+limit;
}