@extends('adminlte::page')

@section('title', 'Editor CR80')

@section('content')
    <div class="row">

        {{-- SIDEBAR --}}
        <div class="col-md-3">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title">Editor CR80</h5>
                </div>

                <div class="card-body">


                    {{-- FRENTE / REVERSO --}}
                    <div class="btn-group w-100 mb-3">
                        <button class="btn btn-primary btn-sm" onclick="switchSide('front')">Frente</button>
                        <button class="btn btn-secondary btn-sm" onclick="switchSide('back')">Reverso</button>
                    </div>

                    {{-- ELEMENTOS --}}
                    <button class="btn btn-primary btn-block mb-2" onclick="addText()">➕ Texto</button>
                    <button class="btn btn-sm btn-info" onclick="cloneSelected()">Clonar</button>
                    <button onclick="deleteSelectedElement()" class="btn btn-danger">Eliminar elemento</button>


                    <button class="btn btn-secondary btn-block mb-2" onclick="addPhoto()">📷 Foto</button>




                    {{-- FONDO --}}
                    <label class="mt-2">Fondo</label>
                    <input type="file" name="background" class="form-control mb-3" onchange="setBackground(this)">

                    {{-- CONTROLES --}}
                    <div id="controls" style="display:none">
                        <hr>
                        <label>Texto</label>
                        <input type="text" class="form-control mb-2" id="ctrlText">

                        <label>Tamaño</label>
                        <input type="number" class="form-control mb-2" id="ctrlSize">

                        <label>Color</label>
                        <input type="color" class="form-control mb-2" id="ctrlColor">

                        <label>Alineación</label>
                        <select class="form-control mb-2" id="ctrlAlign">
                            <option value="left">Izquierda</option>
                            <option value="center">Centro</option>
                            <option value="right">Derecha</option>
                        </select>

                        <label>Variables</label>
                        <select class="form-control mb-2" onchange="insertVariable(this)">
                            <option value="">-- seleccionar --</option>
                            <option value="@{{ nombre }}">Nombre</option>
                            <option value="@{{ apellido }}">Apellido</option>
                            <option value="@{{ cargo }}">Cargo</option>
                            <option value="@{{ codigo }}">Código</option>
                            <option value="@{{ dni }}">DNI</option>
                        </select>


                        <button class="btn btn-danger btn-block" onclick="removeElement()">🗑 Eliminar</button>
                    </div>

                    <hr>
                    <button class="btn btn-success btn-block" onclick="save()">💾 Guardar Diseño</button>
                </div>
            </div>
        </div>

        {{-- EDITOR --}}
        <div class="col-md-9">
            <div id="card-editor">
                <img id="card-bg">
            </div>
        </div>
    </div>

    <form id="formSave" method="POST" action="{{ route('idcards.store') }}">
        @csrf
        <input type="hidden" name="name" value="Diseño CR80">
        <input type="hidden" name="layout" id="layout">
    </form>
@stop

@section('css')
    <style>
        #card-editor {
            width: 340px;
            height: 214px;
            border: 1px solid #999;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        #card-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .draggable {
            position: absolute;
            cursor: move;
            user-select: none;
        }

        .photo {
            width: 70px;
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
        }

        .selected {
            outline: 1px dashed red;
        }
    </style>
@stop

@section('js')
<script>
    const editor = document.getElementById('card-editor');
    const controls = document.getElementById('controls');
// let elements = Array.isArray(data.design_json) 
//                ? data.design_json 
//                : [];

    let selected = null;
    let side = 'front';

    let layouts = {
        front: [],
        back: []
    };

    let backgrounds = {
        front: null,
        back: null
    };

    /* ================= DRAG ================= */

    function makeDraggable(el) {
        el.addEventListener('mousedown', function (e) {
            select(el);

            const editorRect = editor.getBoundingClientRect();
            const elRect = el.getBoundingClientRect();

            const offsetX = e.clientX - elRect.left;
            const offsetY = e.clientY - elRect.top;

            function move(ev) {
                let x = ev.clientX - editorRect.left - offsetX;
                let y = ev.clientY - editorRect.top - offsetY;

                x = Math.max(0, Math.min(x, editor.clientWidth - el.offsetWidth));
                y = Math.max(0, Math.min(y, editor.clientHeight - el.offsetHeight));

                el.style.left = x + 'px';
                el.style.top = y + 'px';
            }

            function up() {
                document.removeEventListener('mousemove', move);
                document.removeEventListener('mouseup', up);
            }

            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        });
    }

    /* ================= SELECCIÓN ================= */

    function select(el) {
        document.querySelectorAll('.selected').forEach(e => e.classList.remove('selected'));
        selected = el;
        el.classList.add('selected');
        controls.style.display = 'block';

        const isImg = el.tagName === 'IMG';

        ctrlText.disabled = isImg;
        ctrlText.value = isImg ? '' : el.innerText;
        ctrlSize.value = parseInt(el.style.fontSize) || 12;
        ctrlColor.value = rgbToHex(el.style.color || '#000');
        ctrlAlign.value = el.style.textAlign || 'left';
    }

    /* ================= ELEMENTOS ================= */

    function addText() {
        const el = document.createElement('div');
        el.className = 'draggable';
        el.innerText = 'Texto';
        el.style.cssText = `
            left:20px;
            top:20px;
            font-size:12px;
            color:#000;
            text-align:left;
        `;
        makeDraggable(el);
        editor.appendChild(el);
        select(el);
    }

    function addPhoto() {
        const img = document.createElement('img');
        img.src = '/img/avatar.png';
        img.className = 'draggable photo';
        img.style.left = '20px';
        img.style.top = '60px';
        makeDraggable(img);
        editor.appendChild(img);
        select(img);
    }

    function cloneSelected() {
        if (!selected) return alert('Selecciona un elemento');

        const clone = selected.cloneNode(true);
        clone.style.left = (parseInt(selected.style.left) + 10) + 'px';
        clone.style.top = (parseInt(selected.style.top) + 10) + 'px';

        makeDraggable(clone);
        editor.appendChild(clone);
        select(clone);
    }

    function removeElement() {
        if (!selected) return;
        selected.remove();
        selected = null;
        controls.style.display = 'none';
    }

    /* ================= CONTROLES ================= */

    ctrlText.oninput = e => {
        if (selected && selected.tagName !== 'IMG') {
            selected.innerText = e.target.value;
        }
    };

    ctrlSize.oninput = e => selected && (selected.style.fontSize = e.target.value + 'px');
    ctrlColor.oninput = e => selected && (selected.style.color = e.target.value);
    ctrlAlign.onchange = e => selected && (selected.style.textAlign = e.target.value);

    function insertVariable(sel) {
        if (selected && selected.tagName !== 'IMG' && sel.value) {
            selected.innerText += sel.value;
            sel.value = '';
        }
    }

    /* ================= GUARDADO ================= */

    function saveCurrent() {
        layouts[side] = [];

        editor.querySelectorAll('.draggable').forEach(el => {
            layouts[side].push({
                type: el.tagName === 'IMG' ? 'photo' : 'text',
                text: el.innerText || '',
                src: el.src || null,
                x: el.style.left,
                y: el.style.top,
                size: el.style.fontSize,
                color: el.style.color,
                align: el.style.textAlign,
                width: el.style.width,
                height: el.style.height
            });
        });
    }

    function loadCurrent() {
        editor.innerHTML = '<img id="card-bg">';

        if (backgrounds[side]) {
            document.getElementById('card-bg').src = backgrounds[side];
        }

        layouts[side].forEach(el => {
            let node;

            if (el.type === 'photo') {
                node = document.createElement('img');
                node.src = el.src;
                node.className = 'draggable photo';
                node.style.width = el.width;
                node.style.height = el.height;
            } else {
                node = document.createElement('div');
                node.className = 'draggable';
                node.innerText = el.text;
                node.style.fontSize = el.size;
                node.style.color = el.color;
                node.style.textAlign = el.align;
            }

            node.style.left = el.x;
            node.style.top = el.y;

            makeDraggable(node);
            editor.appendChild(node);
        });
    }

    function switchSide(s) {
        saveCurrent();
        side = s;
        loadCurrent();
    }

    function setBackground(input) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('card-bg').src = e.target.result;
            backgrounds[side] = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

    function save() {
        saveCurrent();
        document.getElementById('layout').value = JSON.stringify({
            front: layouts.front,
            back: layouts.back,
            backgrounds
        });
        document.getElementById('formSave').submit();
    }

    function rgbToHex(rgb) {
        if (!rgb.startsWith('rgb')) return rgb;
        const c = rgb.match(/\d+/g).map(Number);
        return "#" + c.map(x => x.toString(16).padStart(2, '0')).join('');
    }

    // Selección de elemento
document.querySelectorAll('.element').forEach(el => {
    el.addEventListener('click', () => {
        // Quitar selección de otros
        document.querySelectorAll('.element.selected').forEach(sel => sel.classList.remove('selected'));
        // Marcar este
        el.classList.add('selected');
    });
});


document.querySelectorAll('.element').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.element.selected').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
    });
});


document.addEventListener('keydown', (e) => {
    if (e.key === "Delete") {
        deleteSelectedElement();
    }
});



</script>

@stop
