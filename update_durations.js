// 1. On charge les clés secrètes
require('dotenv').config({ path: '.env.local' });
require('dotenv').config({ path: '.env' });
const mysql = require('mysql2/promise');

const SPOTIFY_CLIENT_ID = process.env.SPOTIFY_CLIENT_ID;
const SPOTIFY_CLIENT_SECRET = process.env.SPOTIFY_CLIENT_SECRET;

if (!SPOTIFY_CLIENT_ID || !SPOTIFY_CLIENT_SECRET) {
    console.error("❌ ERREUR : Node.js ne trouve pas tes clés Spotify !");
    process.exit(1);
}

const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

async function getSpotifyToken() {
    const clientId = SPOTIFY_CLIENT_ID.trim();
    const clientSecret = SPOTIFY_CLIENT_SECRET.trim();
    const auth = Buffer.from(`${clientId}:${clientSecret}`).toString('base64');
    
    // La vraie adresse pour s'authentifier :
    const response = await fetch('https://accounts.spotify.com/api/token', {
        method: 'POST',
        headers: {
            'Authorization': `Basic ${auth}`,
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'grant_type=client_credentials'
    });
    
    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Erreur Auth Spotify (${response.status}): ${errorText}`);
    }
    const data = await response.json();
    return data.access_token;
}

// 3. Recherche de la piste (VRAIE URL OFFICIELLE + ANTI-BAN 429)
async function getTrackDuration(token, artist, title, retryCount = 0) {
    const query = encodeURIComponent(`${artist} ${title}`);
    const searchUrl = `https://api.spotify.com/v1/search?q=${query}&type=track&limit=1`;
    
    const response = await fetch(searchUrl, {
        headers: { 'Authorization': `Bearer ${token}` }
    });

    // LE BOUCLIER ANTI-BAN : Si Spotify dit "Trop vite" (429)
    if (response.status === 429) {
        // On lit combien de secondes Spotify veut qu'on attende (ou 5 sec par défaut)
        const retryAfter = response.headers.get('Retry-After') || 5; 
        console.log(`\n🚦 Vitesse excessive ! Spotify impose une pause de ${retryAfter} secondes...`);
        
        await sleep(retryAfter * 1000); // On met le script en pause
        
        // On relance la fonction pour réessayer ce morceau !
        return getTrackDuration(token, artist, title, retryCount + 1); 
    }

    if (!response.ok) {
        const errorText = await response.text();
        console.error(`\n⚠️ CRASH API SPOTIFY (${response.status}) sur "${artist} - ${title}":`, errorText);
        return null;
    }

    const data = await response.json();
    if (data.tracks && data.tracks.items && data.tracks.items.length > 0) {
        return Math.round(data.tracks.items[0].duration_ms / 1000);
    }
    
    return null; 
}

// 4. Le script principal
async function main() {
    console.log("🚀 Lancement du script V3 (Anti-Null & Vraies URLs)...");

    const connection = await mysql.createConnection({
        host: '192.168.56.201',
        user: 'yoasobi',
        password: 'chpuk',
        database: 'KSwap',
        charset: 'utf8mb4'
    });

    try {
        const token = await getSpotifyToken();
        console.log("✅ Connecté au VRAI Spotify !");

        const [rows] = await connection.execute(`
            SELECT m.id, m.titre, a.nom_scene AS artiste 
            FROM morceau m 
            JOIN discographie d ON m.discographie_id = d.id 
            JOIN artiste a ON d.artiste_id = a.id 
            WHERE m.duree IS NULL
        `);

        if (rows.length === 0) {
            console.log("🎉 TOUT EST FINI ! Plus aucun morceau vide.");
            process.exit(0);
        }

        console.log(`\n📦 ${rows.length} morceaux à traiter trouvés. C'est parti...`);

        let countSaved = 0;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cleanTitle = row.titre.replace(/\(.*?\)|\[.*?\]/g, '').trim();
            
            let duration = await getTrackDuration(token, row.artiste, cleanTitle);

            // NOUVELLE LOGIQUE : On ne met à jour QUE si Spotify a trouvé une durée
            if (duration) {
                const [updateResult] = await connection.execute('UPDATE morceau SET duree = ? WHERE id = ?', [duration, row.id]);
                
                if (updateResult.affectedRows === 0) {
                    console.log(`❌ BUG SQL : L'ID ${row.id} a été ignoré !`);
                } else {
                    countSaved++;
                    console.log(`${i + 1}/${rows.length} | ✅ Spotify : ${row.artiste} - ${cleanTitle} (${duration}s)`);
                }
            } else {
                // Si duration est null, on ne fait rien en BDD, on l'affiche juste
                console.log(`${i + 1}/${rows.length} | ❌ Introuvable : ${row.artiste} - ${cleanTitle} (Reste vide)`);
            }

            // Pause pour ne pas spammer Spotify
            await sleep(500);
        }

        console.log(`\n✅ Opération terminée ! ${countSaved} morceaux mis à jour avec des VRAIS nombres.`);

    } catch (error) {
        console.error("❌ ERREUR :", error.message);
    } finally {
        await connection.end();
    }
}

main();