import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["container", "loading"];
    static values = {
        page: Number,
        total: Number,
        url: String,
    };

    connect() {
        console.log("Infinite Scroll connecté ! Page:", this.pageValue, "/", this.totalValue);

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    console.log("Loader visible -> Chargement demandé...");
                    this.loadMore();
                }
            });
        });

        if (this.hasLoadingTarget) {
            this.observer.observe(this.loadingTarget);
        } else {
            console.error("ERREUR : La div 'loading' est introuvable dans le HTML !");
        }
    }

    async loadMore() {
        if (this.pageValue >= this.totalValue || this.isLoading) return;

        this.isLoading = true;
        const nextPage = this.pageValue + 1;
        console.log(`Chargement de la page ${nextPage}...`);

        try {
            const response = await fetch(`${this.urlValue}?page=${nextPage}&ajax=1`);
            const html = await response.text();

            this.containerTarget.insertAdjacentHTML("beforeend", html);
            this.pageValue = nextPage;
            console.log("Page chargée avec succès !");

            // --- C'EST ICI QUE LA MAGIE OPÈRE ---
            // Si on n'a pas fini de tout charger...
            if (this.pageValue < this.totalValue) {
                // ... et que le loader est TOUJOURS visible (l'écran n'est pas plein)
                if (this.hasLoadingTarget && this.isElementInViewport(this.loadingTarget)) {
                    console.log("L'écran n'est pas rempli, on charge la suite automatiquement...");
                    setTimeout(() => this.loadMore(), 200); // On relance !
                }
            } else {
                // Si c'est fini, on cache le loader
                if (this.hasLoadingTarget) this.loadingTarget.style.display = "none";
                console.log("Tout est chargé !");
            }

        } catch (error) {
            console.error("Erreur chargement :", error);
        } finally {
            this.isLoading = false;
        }
    }

    isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
        );
    }

    disconnect() {
        if (this.observer) this.observer.disconnect();
    }
}