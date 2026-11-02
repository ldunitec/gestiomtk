const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

test('selector: cambia origen, conserva cero, bloquea rutas sin km y restaura al limpiar', () => {
    const plantilla = fs.readFileSync(path.join(__dirname,
        '../../Modules/Produccion/Resources/views/admin/viaticos/create.blade.php'), 'utf8');
    const script = plantilla.match(/<script>([\s\S]*?)<\/script>/)[1];
    const eventos = {};
    const nodo = (id, value = '') => ({
        value,
        addEventListener: (evento, fn) => { eventos[id + ':' + evento] = fn; },
    });
    const origen = nodo('origen', 'Tegucigalpa');
    const ciudad = nodo('ciudad', '1');
    ciudad.selectedIndex = 0;
    const kms = { 'data-tgu': '100.25', 'data-sps': '0.00' };
    ciudad.options = [{ getAttribute: nombre => kms[nombre] ?? null }];
    ciudad.form = nodo('form');
    const nodos = {
        origen, ciudad, distancia_km: nodo('km'), error_distancia: nodo('error'),
        guardar_viatico: nodo('guardar'),
    };
    vm.runInNewContext(script, {
        document: { addEventListener: (_, fn) => fn(), getElementById: id => nodos[id] },
        setTimeout: fn => fn(),
    });
    assert.equal(nodos.distancia_km.value, '100.25');
    assert.equal(nodos.guardar_viatico.disabled, false);
    origen.value = 'San Pedro Sula';
    eventos['origen:change']();
    assert.equal(nodos.distancia_km.value, '0.00');
    assert.equal(nodos.guardar_viatico.disabled, false);
    kms['data-sps'] = '';
    eventos['ciudad:change']();
    assert.equal(nodos.distancia_km.value, '');
    assert.equal(nodos.guardar_viatico.disabled, true);
    assert.match(nodos.error_distancia.textContent, /Falta registrar/);
    ciudad.value = '';
    eventos['ciudad:change']();
    assert.equal(nodos.error_distancia.textContent, '');
    assert.equal(nodos.guardar_viatico.disabled, true);
    ciudad.value = '1';
    origen.value = 'Tegucigalpa';
    eventos['form:reset']();
    assert.equal(nodos.distancia_km.value, '100.25');
    assert.equal(nodos.guardar_viatico.disabled, false);
});
