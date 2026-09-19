@extends('adminlte::page')

@section('title', 'Foto Alumno')

@section('content')
<div class="card">
    <div class="card-header">Foto para Carnet</div>
    <div class="card-body">

        <input type="file" id="inputFoto" class="form-control mb-3" accept="image/*">
<div class="mt-2">
    <button type="button" id="btnIzquierda" class="btn btn-secondary">
        ⟲ Girar Izquierda
    </button>

    <button type="button" id="btnDerecha" class="btn btn-secondary">
        ⟳ Girar Derecha
    </button>

    <button type="button" id="btnReset" class="btn btn-danger">
        Reset
    </button>
</div>
        <div class="row">
            <div class="col-md-8">
                <img id="imagen" style="max-width:100%; display:none;">
            </div>

            <div class="col-md-4">
                <label><b>Vista previa carnet</b></label>
                <div style="width:180px;height:240px;overflow:hidden;border:1px solid #ccc;">
                    <div class="preview"></div>
                </div>
            </div>
        </div>

        <button id="btnGuardar" class="btn btn-success mt-3" disabled>
            Guardar Foto
        </button>

        <form id="formFoto" method="POST" action="{{ route('carnet.foto.store') }}">
            @csrf
            <input type="hidden" name="imagen" id="imagenFinal">
        </form>

    </div>
</div>
@endsection
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet"/>

<style>
.preview {
    width: 180px;
    height: 240px;
    overflow: hidden;
}
</style>
@endsection
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js"></script>
{{-- <script>
let cropper = null;

const input = document.getElementById('inputFoto');
const image = document.getElementById('imagen');
const btnGuardar = document.getElementById('btnGuardar');

const faceDetection = new FaceDetection({
    locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/${file}`;
    }
});

faceDetection.setOptions({
    model: 'short',
    minDetectionConfidence: 0.6
});

input.addEventListener('change', function(e) {

    const reader = new FileReader();

    reader.onload = function(event) {
        image.src = event.target.result;
        image.style.display = 'block';
    };

    reader.readAsDataURL(e.target.files[0]);
});

input.addEventListener('change', function(e) {

    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {

        const img = new Image();
        img.src = event.target.result;

        img.onload = function() {

            EXIF.getData(img, function() {

                const orientation = EXIF.getTag(this, "Orientation");

                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                let width = img.width;
                let height = img.height;

                // 🔥 Ajustar canvas según orientación
                if ([5,6,7,8].includes(orientation)) {
                    canvas.width = height;
                    canvas.height = width;
                } else {
                    canvas.width = width;
                    canvas.height = height;
                }

                // 🔥 Rotaciones
                switch(orientation) {
                    case 2: ctx.transform(-1, 0, 0, 1, width, 0); break;
                    case 3: ctx.transform(-1, 0, 0, -1, width, height); break;
                    case 4: ctx.transform(1, 0, 0, -1, 0, height); break;
                    case 5: ctx.transform(0, 1, 1, 0, 0, 0); break;
                    case 6: ctx.transform(0, 1, -1, 0, height, 0); break;
                    case 7: ctx.transform(0, -1, -1, 0, height, width); break;
                    case 8: ctx.transform(0, -1, 1, 0, 0, width); break;
                    default: break;
                }

                ctx.drawImage(img, 0, 0);

                const finalImage = canvas.toDataURL("image/jpeg");

                image.src = finalImage;
                image.style.display = 'block';
            });
        };
    };

    reader.readAsDataURL(file);
});

    await faceDetection.send({image: img});

btnGuardar.addEventListener('click', function() {

    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({
        width: 600,
        height: 800
    });

    document.getElementById('imagenFinal').value =
        canvas.toDataURL('image/jpeg');

    document.getElementById('formFoto').submit();
});
</script> --}}

    <script>
let cropper = null;

const input = document.getElementById('inputFoto');
const image = document.getElementById('imagen');
const btnGuardar = document.getElementById('btnGuardar');

cropper = new Cropper(image, {
    aspectRatio: 3/4,
    viewMode: 1,
    dragMode: 'move',
    responsive: true,
    restore: false,   // 🔥 IMPORTANTE
    checkOrientation: false, // 🔥 ya usamos EXIF
    preview: '.preview',
    autoCropArea: 1,

});
// 🔥 BOTONES (FUERA DE TODO)
document.getElementById('btnIzquierda').addEventListener('click', () => {
    if (!cropper) return;

    cropper.rotate(-90);

    setTimeout(() => {
        cropper.reset();      // 🔥 fuerza actualización visual
        cropper.rotate(-90);  // 🔥 reaplica rotación
        recenterCrop();
    }, 50);
});

document.getElementById('btnDerecha').addEventListener('click', () => {
    if (!cropper) return;

    cropper.rotate(90);

    setTimeout(() => {
        cropper.reset();
        cropper.rotate(90);
        recenterCrop();
    }, 50);
});

document.getElementById('btnReset').addEventListener('click', () => {
    if (cropper) {
        cropper.reset();
    }
});

// 🔥 RE-CENTRAR CROP (OPCIONAL PRO)
function recenterCrop() {
    if (!cropper) return;

    const canvasData = cropper.getCanvasData();
    const cropBoxData = cropper.getCropBoxData();

    cropper.setCropBoxData({
        left: canvasData.left + (canvasData.width - cropBoxData.width) / 2,
        top: canvasData.top + (canvasData.height - cropBoxData.height) / 2
    });
}

// 🔥 CONFIG FACE DETECTION
const faceDetection = new FaceDetection({
    locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/${file}`;
    }
});

faceDetection.setOptions({
    model: 'short',
    minDetectionConfidence: 0.6
});

// 🔥 CUANDO SE SELECCIONA IMAGEN
input.addEventListener('change', function(e) {

    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = function(event) {

        const img = new Image();
        img.src = event.target.result;

        img.onload = function() {

            EXIF.getData(img, function() {

                const orientation = EXIF.getTag(this, "Orientation") || 1;

                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                let width = img.width;
                let height = img.height;

                // 🔥 Ajustar canvas según orientación
                if ([5,6,7,8].includes(orientation)) {
                    canvas.width = height;
                    canvas.height = width;
                } else {
                    canvas.width = width;
                    canvas.height = height;
                }

                // 🔥 Rotar imagen correctamente
                switch(orientation) {
                    case 2: ctx.transform(-1, 0, 0, 1, width, 0); break;
                    case 3: ctx.transform(-1, 0, 0, -1, width, height); break;
                    case 4: ctx.transform(1, 0, 0, -1, 0, height); break;
                    case 5: ctx.transform(0, 1, 1, 0, 0, 0); break;
                    case 6: ctx.transform(0, 1, -1, 0, height, 0); break;
                    case 7: ctx.transform(0, -1, -1, 0, height, width); break;
                    case 8: ctx.transform(0, -1, 1, 0, 0, width); break;
                }

                ctx.drawImage(img, 0, 0);

                const finalImage = canvas.toDataURL("image/jpeg", 0.95);

                image.src = finalImage;
                image.style.display = 'block';

                // 🔥 CUANDO YA ESTÁ ROTADA
                image.onload = function() {

                    const tempImg = new Image();
                    tempImg.src = finalImage;

                    faceDetection.onResults(results => {

                        if(results.detections.length === 0){
                            alert("No se detectó rostro");
                            return;
                        }

                        const box = results.detections[0].boundingBox;

                        const wImg = image.naturalWidth;
                        const hImg = image.naturalHeight;

                        const x = box.xCenter * wImg - (box.width * wImg / 2);
                        const y = box.yCenter * hImg - (box.height * hImg / 2);
                        const w = box.width * wImg;
                        const h = box.height * hImg;

                        if(cropper) cropper.destroy();

                        cropper = new Cropper(image, {
                            aspectRatio: 3/4,
                            viewMode: 1,
                            dragMode: 'move',
                            preview: '.preview',
                            autoCropArea: 1,

                            ready() {

                                // 🔥 AJUSTE PRO CARNET
                                const faceHeight = h;
                                const targetHeadRatio = 0.70;

                                const finalHeight = faceHeight / targetHeadRatio;

                                const expandTop = finalHeight * 0.35;
                                const expandSides = finalHeight * 0.40;

                                cropper.setData({
                                    x: x - expandSides,
                                    y: y - expandTop,
                                    width: finalHeight * (3/4),
                                    height: finalHeight
                                });

                                btnGuardar.disabled = false;
                            }
                        });

                    });

                    faceDetection.send({image: tempImg});
                };
            });
        };
    };

    reader.readAsDataURL(file);
});

// 🔥 GUARDAR IMAGEN FINAL
btnGuardar.addEventListener('click', function() {

    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({
        width: 450,
        height: 600,
        fillColor: '#fff'
    });

    document.getElementById('imagenFinal').value =
        canvas.toDataURL('image/jpeg', 0.95);

    document.getElementById('formFoto').submit();
});

</script>
@endsection