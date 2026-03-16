const ytSearch = require("yt-search");
const query = process.argv.slice(2).join(" ");

if (!query) {
    console.log("NOT_FOUND");
    process.exit(1);
}

ytSearch(query)
    .then((r) => {
        if (r && r.videos && r.videos.length > 0) {
            // 1. On cherche UNIQUEMENT une vidéo officielle YouTube Music ("Topic" ou "Thème")
            const topicVideo = r.videos.find(
                (v) =>
                    v.author &&
                    (v.author.name.includes("Topic") ||
                        v.author.name.includes("Thème")),
            );

            if (topicVideo) {
                console.log(topicVideo.url);
            } else {
                // 2. S'il n'y a pas de Topic, on cherche une vidéo de fan "Lyrics" pour esquiver le blocage
                const lyricsVideo = r.videos.find((v) =>
                    v.title.toLowerCase().includes("lyrics"),
                );
                console.log(lyricsVideo ? lyricsVideo.url : r.videos[0].url);
            }
        } else {
            console.log("NOT_FOUND");
        }
    })
    .catch((err) => {
        console.log("ERROR");
    });
