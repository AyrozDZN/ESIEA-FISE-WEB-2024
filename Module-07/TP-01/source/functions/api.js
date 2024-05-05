const adresseInput = document.getElementById('adresseInput');
const suggestionsList = document.getElementById('suggestionsList');
let marker = null;

adresseInput.addEventListener('input', function() {
    const inputValue = adresseInput.value;

    // Utilisez l'API BAN pour récupérer les suggestions en fonction de inputValue
    // Exemple d'URL de l'API BAN : https://api-adresse.data.gouv.fr/search/?q=${inputValue}
    // Vous pouvez utiliser fetch() ou une bibliothèque comme axios pour faire la requête.

    // Ensuite, traitez la réponse de l'API et affichez les suggestions.
    fetch(`https://api-adresse.data.gouv.fr/search/?q=${inputValue}`)
        .then(response => response.json())
        .then(data => {
            const suggestions = data.features;
            afficherSuggestions(suggestions);
        })
        .catch(error => console.error('Erreur lors de la récupération des suggestions:', error));
});

function afficherSuggestions(suggestions) {
    suggestionsList.innerHTML = ''; // Efface les anciennes suggestions

    suggestions.forEach(suggestion => {
        const option = document.createElement('option');
        option.value = `${suggestion.properties.label}`;
        option.setAttribute("long", suggestion.geometry.coordinates[0]);
        option.setAttribute("lat", suggestion.geometry.coordinates[1]);
        suggestionsList.appendChild(option);
    });
}

adresseInput.addEventListener('change',function(event){

    const label = event.target.value
    var lat = document.querySelector(`#suggestionsList option[value='${label}']`).getAttribute("lat");
    var long = document.querySelector(`#suggestionsList option[value='${label}']`).getAttribute("long");
    console.log(label, lat, long)


    if (marker != null) map.removeLayer(marker)
    marker = L.marker([lat, long]).addTo(map);
    marker.bindPopup(label).openPopup();
    map.setView([lat, long], 13);
});
