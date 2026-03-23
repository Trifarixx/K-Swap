const ytSearch = require("yt-search");
const query = process.argv.slice(2).join(" ");

if (!query) {
    console.log("NOT_FOUND");
    process.exit(1);
}

// On ajoute les mots-clés magiques à ta recherche initiale
const safeQuery = query + " official audio OR lyrics";

ytSearch(safeQuery)
    .then((r) => {
        if (r && r.videos && r.videos.length > 0) {
            // On filtre pour être sûr de choper une version audio ou lyrics
            let selectedVideo = r.videos.find(
                (v) =>
                    v.title.toLowerCase().includes("audio") ||
                    v.title.toLowerCase().includes("lyrics") ||
                    (v.author && v.author.name.includes("Topic")),
            );

            // Sécurité : si vraiment on trouve rien de précis, on prend le 1er résultat
            if (!selectedVideo) {
                selectedVideo = r.videos[0];
            }

            console.log(selectedVideo.url);
        } else {
            console.log("NOT_FOUND");
        }
    })
    .catch((err) => {
        console.log("ERROR");
    });
