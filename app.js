// Main Modules connecting
const express = require('express');
const appRoutes = require('./routes/appRoutes.js')
const bodyParser = require('body-parser')


// Express init
const app = express();
const port = process.env.PORT || 8000;
app.use(bodyParser.json()); 


app.listen(port, () => {
  console.log(`Example app listening at http://127.0.0.1:${port}`);
})

app.use('/', appRoutes);
