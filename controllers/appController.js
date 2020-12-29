const sqlite3 = require('sqlite3').verbose();
const sha1 = require('sha1')


const appLogin = (req, res) => {
    let db = new sqlite3.Database('./db/clients.db');

    let username = req.query.username
    let password = sha1(`${req.query.pass}`)
    let sql = `SELECT * FROM users WHERE username = "${username}" AND password = "${password}"`;

    db.all(sql, [], (err, rows) => {
        if (err) {
            res.send('Username or password is wrong.')
        }
        res.send('Success')
    });
}


module.exports = {
    appLogin
}