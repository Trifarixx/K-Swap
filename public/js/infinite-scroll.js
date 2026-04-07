document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("feed-container");
    const trigger = document.getElementById("loading-trigger");

    // Sécurité : si les éléments n'existent pas, on arrête
    if (!container || !trigger) return;

    let nextPage = parseInt(container.dataset.nextPage);
    let totalPages = parseInt(container.dataset.totalPages);
    let isLoading = false;

    // Si on n'a qu'une seule page, on cache le loader tout de suite
    if (nextPage > totalPages) {
        trigger.style.display = "none";
        return;
    } else {
        trigger.style.display = "block";
    }

    // Fonction de chargement
    const loadMore = () => {
        if (isLoading || nextPage > totalPages) return;

        isLoading = true;

        // On appelle le contrôleur avec ?page=X&ajax=1
        fetch(`/?page=${nextPage}&ajax=1`)
            .then((response) => {
                if (!response.ok) throw new Error("Erreur réseau");
                return response.text();
            })
            .then((html) => {
                // On ajoute le HTML à la fin du conteneur
                container.insertAdjacentHTML("beforeend", html);

                nextPage++;
                isLoading = false;

                // Si on a tout chargé, on cache le loader
                if (nextPage > totalPages) {
                    trigger.style.display = "none";
                }
            })
            .catch((err) => {
                console.error(err);
                isLoading = false;
            });
    };

    // L'Observateur : détecte quand le trigger entre dans l'écran
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    loadMore();
                }
            });
        },
        {
            rootMargin: "200px", // On charge un peu avant d'arriver tout en bas pour la fluidité
        },
    );

    observer.observe(trigger);
});
