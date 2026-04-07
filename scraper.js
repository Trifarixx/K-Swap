const axios = require("axios");
const cheerio = require("cheerio");
const fs = require("fs");

const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

/**
 * ÉTAPE 1 : Récupérer tous les liens de profils
 */
async function getGroupLinks(listUrl) {
    console.log(`\n🔍 Récupération des groupes depuis : ${listUrl}`);
    try {
        const response = await axios.get(listUrl);
        const $ = cheerio.load(response.data);
        let links = [];

        $(".entry-content a").each(function () {
            const href = $(this).attr("href");
            if (
                href &&
                href.includes("-profile") &&
                !href.includes("disbanded")
            ) {
                if (!links.includes(href)) {
                    links.push(href);
                }
            }
        });

        console.log(`✅ ${links.length} liens trouvés !`);
        return links;
    } catch (error) {
        console.error(`❌ Erreur sur la liste :`, error.message);
        return [];
    }
}

/**
 * ÉTAPE 2 : Aspirer les membres d'une page ET le nom du groupe
 */
async function scrapeGroupMembers(url) {
    try {
        const response = await axios.get(url);
        const $ = cheerio.load(response.data);
        let members = [];

        // --- NOUVEAUTÉ : EXTRACTION DU NOM DU GROUPE ---
        // On récupère le grand titre H1 de la page (ex: "FIFTY FIFTY Members Profile")
        let pageTitle = $("h1.entry-title").text().trim();

        // On nettoie le titre pour ne garder QUE le nom du groupe
        // Ça retire " Members Profile", " Profile", " Members Profile and Facts", etc.
        let groupName = pageTitle
            .replace(/\s*(members profile|profile).*$/i, "")
            .trim();

        // On cherche les membres
        $("p, div").each(function () {
            const text = $(this).text();
            const match = text.match(/Stage.*Name:\s*([^\(<]+)/i);

            if (match) {
                let name = match[1].trim().replace(/\u00A0/g, " ");

                if (members.find((m) => m.nom === name)) return;

                let imgNode = $(this).find("img").first();
                if (imgNode.length === 0) {
                    imgNode = $(this)
                        .prevAll("p, div, center")
                        .first()
                        .find("img")
                        .last();
                }

                if (imgNode.length > 0) {
                    const src = imgNode.attr("src");
                    if (src && src.includes("wp-content/uploads")) {
                        members.push({
                            nom: name,
                            groupe: groupName, // <--- AJOUT DU GROUPE ICI
                            image: src,
                        });
                    }
                }
            }
        });
        return members;
    } catch (error) {
        console.error(`⚠️ Impossible d'aspirer ${url}`);
        return [];
    }
}

/**
 * ÉTAPE 3 : Orchestration globale
 */
async function scrapeEverything() {
    let allIdols = [];

    const listsToCrawl = [
        "https://kprofiles.com/k-pop-girl-groups/",
        "https://kprofiles.com/k-pop-boy-groups/",
    ];

    let allGroupLinks = [];

    for (const listUrl of listsToCrawl) {
        const links = await getGroupLinks(listUrl);
        allGroupLinks = allGroupLinks.concat(links);
    }

    // 🛑 LIGNE DE TEST (Décommente pour tester sur 5 groupes seulement)
    // allGroupLinks = allGroupLinks.slice(0, 5);

    console.log(
        `\n🚀 Lancement du grand scraping de ${allGroupLinks.length} groupes...`,
    );

    for (let i = 0; i < allGroupLinks.length; i++) {
        const groupUrl = allGroupLinks[i];
        console.log(
            `[${i + 1}/${allGroupLinks.length}] Scraping de : ${groupUrl}`,
        );

        const members = await scrapeGroupMembers(groupUrl);

        if (members && members.length > 0) {
            allIdols = allIdols.concat(members);
            console.log(
                `   -> ${members.length} membres de ${members[0].groupe} ajoutés.`,
            );
        }

        // ⏱️ PAUSE DE SÉCURITÉ DE 2 SECONDES OBLIGATOIRE
        await delay(2000);
    }

    const outputFilename = "all_kpop_idols_with_groups.json";
    fs.writeFileSync(outputFilename, JSON.stringify(allIdols, null, 4), "utf8");

    console.log(`\n🎉 Scraping terminé !`);
    console.log(
        `💾 ${allIdols.length} profils sauvegardés dans '${outputFilename}'.`,
    );
}

scrapeEverything();
