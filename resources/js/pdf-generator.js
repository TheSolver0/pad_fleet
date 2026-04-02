// Générateur PDF pour diagnostics et bons de travail
document.addEventListener('DOMContentLoaded', function() {
    // Écouter les événements de téléchargement PDF
    // Livewire dispatch() events support
    Livewire.on('download-diagnostic-pdf', function(event) {
        const id = event?.id || null;
        if (id) generateDiagnosticPDF(id);
    });

    Livewire.on('download-work-order-pdf', function(event) {
        const id = event?.id || null;
        if (id) generateWorkOrderPDF(id);
    });

    // Fallbacks (DOM custom event style)
    window.addEventListener('download-diagnostic-pdf', function(event) {
        const id = event?.detail?.id || null;
        if (id) generateDiagnosticPDF(id);
    });

    window.addEventListener('download-work-order-pdf', function(event) {
        const id = event?.detail?.id || null;
        if (id) generateWorkOrderPDF(id);
    });

    // Génération PDF pour diagnostic
    function generateDiagnosticPDF(id) {
        // Afficher un indicateur de chargement
        showLoading('Génération du PDF en cours...');

        // Récupérer les données du diagnostic
        fetch(`/api/diagnostics/${id}/pdf`)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `diagnostic-${id}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                hideLoading();
            })
            .catch(error => {
                console.error('Erreur lors de la génération du PDF:', error);
                hideLoading();
                alert('Erreur lors de la génération du PDF');
            });
    }

    // Génération PDF pour bon de travail
    function generateWorkOrderPDF(id) {
        // Afficher un indicateur de chargement
        showLoading('Génération du PDF en cours...');

        // Ouvrir le PDF dans un nouvel onglet
        const url = `/api/work-orders/${id}/pdf`;
        window.open(url, '_blank');

        // Masquer le loading après un court délai
        setTimeout(() => {
            hideLoading();
        }, 1000);
    }

    // Fonctions utilitaires
    function showLoading(message) {
        const loading = document.createElement('div');
        loading.id = 'pdf-loading';
        loading.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-50';
        loading.style.zIndex = '9999';
        loading.innerHTML = `
            <div class="bg-white p-4 rounded shadow">
                <div class="spinner-border text-primary me-2" role="status"></div>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(loading);
    }

    function hideLoading() {
        const loading = document.getElementById('pdf-loading');
        if (loading) {
            loading.remove();
        }
    }
});
