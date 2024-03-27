const http = require('node:http');
const sqlite3 = require('sqlite3');
const fs = require('fs');
const { exec } = require('node:child_process');
const hostname = '0.0.0.0';
const port = 8000;

var HTTP = fs.readFileSync('index.html');
const CSS = fs.readFileSync('style.css');
const SCRIPT = fs.readFileSync('script.js');
const db = new sqlite3.Database('../boards.db');
const { printQueryResults } = require('./utils');
var request_url;


const server = http.createServer((req, res) => {
  
  request_url = req.url;
  console.log(`request : "${request_url}"`);
  var response = HTTP;
  var content_type = 'text/html';

  console.log(request_url);

  switch (request_url) {
    case '/style.css':
      response = CSS;
      content_type = 'text/css';
      break;

    case '/script.js':
      response = SCRIPT;
      content_type = 'text/javascript';
      break;
  
    case '/127.0.0.1:8000/data':
    
    default:
      break;
  }

  res.statusCode = 200;
  res.setHeader('Content-Type', content_type);
  res.end(response);
});



server.listen(
  port, 
  hostname, 
  () => {
  console.log(`Server running at http://${hostname}:${port}/`);
});


// console.log(JSON.stringify(db.all("SELECT * FROM boards")))