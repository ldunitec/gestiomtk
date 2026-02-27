const { Client } = require('node-ssdp');
const axios = require('axios');

const client = new Client();

console.log('🔍 Buscando Roku en la red...');

client.on('response', async (headers, statusCode, rinfo) => {
    if (headers.SERVER && headers.SERVER.includes('Roku')) {
        const ip = rinfo.address;
        console.log('✅ Roku encontrada en:', ip);

        // Obtener info del dispositivo
        const info = await axios.get(`http://${ip}:8060/query/device-info`);
        console.log(info.data);

        client.stop();
    }
});

client.search('roku:ecp');
