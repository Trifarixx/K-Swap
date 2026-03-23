require('dotenv').config({ path: '.env.local' });
const mysql = require('mysql2/promise');

// Petite pause par politesse (Apple est cool, mais on ne va pas spammer)
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function main() {
    let connection;
    try {
        // 1. Connexion à la base de données
        connection = await mysql.createConnection({
            host: '192.168.56.201',
            user: 'yoasobi',
            password: 'chpuk',
            database: 'KSwap', // Modifie si ton nom de base est différent
            port: 3306
        });

        console.log('🔌 Connecté à MariaDB.');

        // On garde le LIMIT 2 pour tester cette nouvelle sécurité en douceur
        const [artistes] = await connection.execute('SELECT id, nom_scene FROM artiste');

        for (const artiste of artistes) {
            console.log(`\n--- 🍏 Recherche iTunes pour : ${artiste.nom_scene} ---`);
            
            // ÉTAPE 1 : Chercher la fiche de l'ARTISTE (et non plus des albums au hasard)
            const searchArtistUrl = `https://itunes.apple.com/search?term=${encodeURIComponent(artiste.nom_scene)}&entity=musicArtist&limit=3`;
            const responseArtist = await fetch(searchArtistUrl);
            const dataArtist = await responseArtist.json();
            await sleep(500); 

            if (!dataArtist || dataArtist.resultCount === 0) {
                console.log(`❌ Artiste introuvable sur iTunes.`);
                continue;
            }

            // ÉTAPE 2 : Le bouclier anti-faux-positifs
            const itunesArtist = dataArtist.results[0];
            const artistNameDb = artiste.nom_scene.toLowerCase();
            const itunesName = itunesArtist.artistName.toLowerCase();

            // Si les noms n'ont rien à voir (ex: O.A.Be vs Common), on ignore l'artiste
            if (!itunesName.includes(artistNameDb) && !artistNameDb.includes(itunesName)) {
                 console.log(`⚠️ Sécurité activée : iTunes propose "${itunesArtist.artistName}" au lieu de "${artiste.nom_scene}". Rejeté.`);
                 continue;
            }

            console.log(`🎯 Artiste validé : ${itunesArtist.artistName} (ID Apple: ${itunesArtist.artistId})`);

            // ÉTAPE 3 : Récupérer UNIQUEMENT les albums de CET artiste précis
            const albumsUrl = `https://itunes.apple.com/lookup?id=${itunesArtist.artistId}&entity=album`;
            const responseAlbums = await fetch(albumsUrl);
            const dataAlbums = await responseAlbums.json();
            await sleep(500);

            // iTunes renvoie la fiche artiste ET les albums dans le même paquet, on filtre
            const albums = dataAlbums.results.filter(item => item.wrapperType === 'collection');

            if (albums.length === 0) {
                console.log(`❌ Aucun album trouvé pour cet ID.`);
                continue;
            }

            for (const album of albums) {
                const [existingAlbum] = await connection.execute(
                    'SELECT id FROM discographie WHERE titre = ? AND artiste_id = ?',
                    [album.collectionName, artiste.id]
                );

                let discographieId;

                if (existingAlbum.length > 0) {
                    discographieId = existingAlbum[0].id;
                    console.log(`⏩ Album existant : ${album.collectionName}`);
                } else {
                    const dateSortie = album.releaseDate ? album.releaseDate.substring(0, 10) : null;
                    const pochette = album.artworkUrl100 ? album.artworkUrl100.replace('100x100bb', '600x600bb') : null;
                    
                    let type = 'Album';
                    if (album.collectionName.toLowerCase().includes('single') || album.collectionName.toLowerCase().includes('ep')) {
                        type = 'EP';
                    }

                    const [result] = await connection.execute(
                        'INSERT INTO discographie (titre, date_sortie, pochette, type, artiste_id) VALUES (?, ?, ?, ?, ?)',
                        [album.collectionName, dateSortie, pochette, type, artiste.id]
                    );
                    discographieId = result.insertId;
                    console.log(`✅ Nouvel album ajouté : ${album.collectionName}`);
                }

                // ÉTAPE 4 : Les pistes de l'album
                const tracksUrl = `https://itunes.apple.com/lookup?id=${album.collectionId}&entity=song`;
                const responseTracks = await fetch(tracksUrl);
                const dataTracks = await responseTracks.json();
                await sleep(500);

                if (!dataTracks || dataTracks.resultCount === 0) continue;

                const tracks = dataTracks.results.filter(item => item.wrapperType === 'track');

                for (const track of tracks) {
                    const [existingTrack] = await connection.execute(
                        'SELECT id FROM morceau WHERE titre = ? AND discographie_id = ?',
                        [track.trackName, discographieId]
                    );

                    if (existingTrack.length === 0) {
                        const dureeSec = track.trackTimeMillis ? Math.round(track.trackTimeMillis / 1000) : null;
                        await connection.execute(
                            'INSERT INTO morceau (titre, duree, discographie_id) VALUES (?, ?, ?)',
                            [track.trackName, dureeSec, discographieId]
                        );
                        console.log(`   🎵 + ${track.trackName} (${dureeSec}s)`);
                    }
                }
            }
        }
        console.log('\n🎉 IMPORTATION SÉCURISÉE TERMINÉE !');

    } catch (error) {
        console.error('💥 ERREUR CRITIQUE :', error);
    } finally {
        if (connection) {
            await connection.end();
            console.log('👋 Déconnexion.');
        }
    }
}

main();