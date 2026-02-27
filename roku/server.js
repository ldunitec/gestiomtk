const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();
const ROKU_IP = '172.20.10.2'; // tu Roku

app.use(express.static(path.join(__dirname, 'public')));

// Obtener lista de apps
app.get('/apps', async (req,res)=>{
    try{
        const response = await axios.get(`http://${ROKU_IP}:8060/query/apps`);
        res.set('Content-Type','application/xml');
        res.send(response.data);
    }catch(e){
        res.status(500).send('No se pudo obtener apps');
    }
});

// Lanzar app
app.get('/launch/:id', async (req,res)=>{
    try{
        await axios.post(`http://${ROKU_IP}:8060/launch/${req.params.id}`);
        res.json({ok:true});
    }catch(e){
        res.status(500).json({error:e.message});
    }
});

app.listen(3000,()=>console.log('🎮 Control Roku Móvil → http://localhost:3000'));
