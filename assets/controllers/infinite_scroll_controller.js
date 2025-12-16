import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["container", "loading"];
    static values = {
        page: Number,
        total: Number,
        url: String,
    };

    connect() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    this.loadMore();
                }
            });
        });

        // On commence à observer l'élément "loading" (le spinner en bas)
        if (this.loadingTarget) {
            this.observer.observe(this.loadingTarget);
        }
    }

    async loadMore() {
        // Si on a déjà tout chargé ou si on est déjà en train de charger, on arrête
        if (this.pageValue >= this.totalValue || this.isLoading) return;

        this.isLoading = true;
        const nextPage = this.pageValue + 1;

        // On appelle le serveur
        const response = await fetch(`${this.url}?page=${nextPage}&ajax=1`);
        const html = await response.text();

        // On ajoute le contenu à la fin de la liste
        this.containerTarget.insertAdjacentHTML("beforeend", html);

        // On met à jour la page actuelle
        this.pageValue = nextPage;
        this.isLoading = false;

        // Si on a atteint la dernière page, on cache le loader
        if (this.pageValue >= this.totalValue) {
            this.loadingTarget.style.display = "none";
        }
    }

    disconnect() {
        if (this.observer) this.observer.disconnect();
    }
}
