document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form_lote');
    if (!form) return;
    const checks = Array.from(document.querySelectorAll('.alumno_seleccionado'));
    const all = document.getElementById('seleccionar_todos');
    const count = document.getElementById('seleccionados');
    function update() {
        const selected = checks.filter(c => c.checked).length;
        count.textContent = String(selected);
        all.checked = checks.length > 0 && selected === checks.length;
        all.indeterminate = selected > 0 && selected < checks.length;
    }
    all.addEventListener('change', () => { checks.forEach(c => { c.checked = all.checked; }); update(); });
    checks.forEach(c => c.addEventListener('change', update));
    update();
    form.addEventListener('submit', async function (event) {
        const button = event.submitter;
        if (!button || button.dataset.action !== 'zip') return;
        event.preventDefault();
        const message = document.getElementById('resultado_zip');
        message.hidden = false;
        message.className = 'alert alert-info';
        message.textContent = 'Preparando ZIP…';
        const payload = new FormData(form);
        button.disabled = true;
        try {
            const response = await fetch(button.formAction, {
                method: 'POST', body: payload,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(Object.values(error.errors || {}).flat().join(' ') || error.message || 'No se pudo generar el ZIP.');
            }
            if (!(response.headers.get('Content-Type') || '').includes('zip')) {
                throw new Error('La sesión pudo haber vencido. Actualiza el panel e inicia sesión nuevamente.');
            }
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = (response.headers.get('Content-Disposition') || '').match(/filename="?([^";]+)"?/)?.[1] || 'FOTOS.zip';
            document.body.appendChild(link);
            link.click();
            link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 60000);
            message.className = 'alert alert-success';
            message.textContent = 'ZIP listo: ' + response.headers.get('X-Fotos-Incluidas') + ' fotos incluidas; '
                + response.headers.get('X-Fotos-Faltantes') + ' sin foto, sin marcar. Pulsa «Filtrar / Actualizar panel» para ver los estados.';
        } catch (error) {
            message.className = 'alert alert-danger';
            message.textContent = error.message;
        } finally {
            button.disabled = false;
        }
    });
});
