var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', { title: 'Express' });
    // res.sendfile('views/index.jade');
});

router.get('/users', function(req, res, next) {
    res.sendfile('views/users.html');
});

module.exports = router;
