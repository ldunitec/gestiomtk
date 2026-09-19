qz.websocket.connect().then(() => {

    return qz.printers.find("ZDesigner ZD510");

}).then(function(printer){

    let config = qz.configs.create(printer);

    let data = [
        document.getElementById('zpl').value
    ];

    return qz.print(config, data);

}).catch(err => {
    console.error(err);
});

function previewEtiqueta(){

    let fecha = document.getElementById('fecha').value;
    let expediente = document.getElementById('expediente').value;
    let nombre = document.getElementById('nombre').value;
    let dni = document.getElementById('dni').value;
    let sala = document.getElementById('sala').value;
    let tipo = document.getElementById('tipo').value;

    document.getElementById('preview').innerHTML = `
        <h3>MEDICAR</h3>
        <p><b>Etiqueta:</b> 1" x ${tipo}"</p>
        <p><b>Fecha:</b> ${fecha}</p>
        <p><b>Expediente:</b> ${expediente}</p>
        <p><b>Nombre:</b> ${nombre}</p>
        <p><b>DNI:</b> ${dni}</p>
        <p><b>Sala:</b> ${sala}</p>
    `;
}

function generarZPL(){

    let fecha = document.getElementById('fecha').value;
    let expediente = document.getElementById('expediente').value;
    let nombre = document.getElementById('nombre').value;
    let dni = document.getElementById('dni').value;
    let sala = document.getElementById('sala').value;
    let tipo = document.getElementById('tipo').value;

    let largo = tipo == "6" ? 1219 : 2235;

    let zpl = `
^XA
^PW800
^LL${largo}

^FO20,20^A0N,40,40^FDMEDICAR^FS

^FO20,90^A0N,30,30^FDFecha: ${fecha}^FS

^FO20,140^A0N,30,30^FDExpediente: ${expediente}^FS

^FO20,190^A0N,30,30^FDNombre:^FS
^FO20,230^A0N,35,35^FD${nombre}^FS

^FO20,300^A0N,30,30^FDDNI: ${dni}^FS

^FO20,350^A0N,30,30^FDSala: ${sala}^FS

^FO20,420^BY2
^BCN,80,Y,N,N
^FD${expediente}^FS

^XZ
`;

    document.getElementById('zpl').value = zpl;
}

function generarZPL(){

    let fecha=document.getElementById("fecha").value;
    let expediente=document.getElementById("expediente").value;
    let nombre=document.getElementById("nombre").value;
    let dni=document.getElementById("dni").value;
    let sala=document.getElementById("sala").value;

    return `
^XA

^PW800
^LL1200

^FO20,20
^A0N,40,40
^FDMEDICAR^FS

^FO20,80
^A0N,30,30
^FDFecha: ${fecha}^FS

^FO20,130
^A0N,30,30
^FDExpediente: ${expediente}^FS

^FO20,180
^A0N,30,30
^FDNombre: ${nombre}^FS

^FO20,230
^A0N,30,30
^FDDNI: ${dni}^FS

^FO20,280
^A0N,30,30
^FDSala: ${sala}^FS

^FO20,350
^BY3
^BCN,100,Y,N,N
^FD${expediente}^FS

^FO500,80
^BQN,2,6
^FDLA,EXP:${expediente},DNI:${dni}^FS

^XZ
`;
}
async function imprimirZebra(){

    try{

        await qz.websocket.connect();

        const printer =
        await qz.printers.find("ZDesigner ZD510");

        const config =
        qz.configs.create(printer);

        const data = [
            generarZPL()
        ];

        await qz.print(config,data);

        alert("Etiqueta enviada correctamente");

        qz.websocket.disconnect();

    }
    catch(error){

        alert(error);

    }
}