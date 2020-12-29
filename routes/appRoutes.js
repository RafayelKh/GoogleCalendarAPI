const express = require('express');
const appController = require('../controllers/appController.js')
const router = express.Router();
const bodyParser = require('body-parser');
var urlencodedParser = bodyParser.urlencoded({ extended: true });


router.post('/loginUser', urlencodedParser, appController.appLogin)


module.exports = router