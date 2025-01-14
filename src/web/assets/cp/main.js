(() => {
    const handler = (event) => {
        new Craft.CpScreenSlideout('element-seeder/seeder/element-matrix-modal', {
            showHeader: true,
            params: {
                elementId: event.currentTarget.dataset.elementId
            }
        });
    }
    const seederBtn = document.querySelectorAll('.seed-element');

    seederBtn.forEach(el => {
        el.addEventListener('click', handler);
    })


    const seedContentHandler = (event) => {
        new Craft.CpScreenSlideout('element-seeder/seeder/element-content-modal', {
            showHeader: true,
            params: {
                elementId: event.currentTarget.dataset.elementId
            }
        });
    }
    document.querySelectorAll('.seed-element-content').forEach(el => {
        el.addEventListener('click', seedContentHandler);
    });
})();
