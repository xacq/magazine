(() => {
    const script = document.currentScript;
    const collectionUrl = script?.dataset.iconCollection;

    async function registerLocalIcons() {
        if (!collectionUrl) {
            throw new Error('No se configuró la colección local de iconos.');
        }

        await customElements.whenDefined('iconify-icon');

        const response = await fetch(collectionUrl, { cache: 'force-cache' });

        if (!response.ok) {
            throw new Error(`No se pudo cargar la colección de iconos (${response.status}).`);
        }

        const collection = await response.json();
        const IconifyIcon = customElements.get('iconify-icon');

        if (!IconifyIcon.addCollection(collection, 'local')) {
            throw new Error('Iconify rechazó la colección local.');
        }

        document.documentElement.classList.add('icons-ready');
    }

    registerLocalIcons().catch((error) => {
        console.error('[Magazine] Error al iniciar los iconos locales:', error);
    });
})();
